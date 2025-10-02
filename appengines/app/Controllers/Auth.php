<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MyModel;
use App\Models\AuthModel;
use App\Services\EmailServices;

class Auth extends Controller
{
  public function index()
  {
    $session = session();
    if ($session->get('logged_in') == FALSE) {
      helper('form');
      $redirect = $this->request->getGet('redirect');
      $data = ['redirect' => $redirect];
      return view('auth/v_login', $data);
    } else {
      return redirect()->route('home');
    }
  }

  public function act()
  {
    helper('form');
    $session = session();
    $model = new AuthModel();

    $username = $this->request->getPost('usr');
    $password = $this->request->getPost('pwd');
    $data = $model->where('username', $username)->first();
    if ($data) {
      $hash = $data['password'];
      $verify_pass = password_verify($password, $hash);
      if ($verify_pass) {
        if ($data['status_user'] == 1) {
          date_default_timezone_set('Asia/Singapore');
          $datenow = date('Y-m-d H:i:s');
          $model->update(array('id_user' => $data['id_user']), array('last_login' => $datenow));

          $ses_data = [
            'id_user'        => $data['id_user'],
            'username'      => $data['username'],
            'nama'      => $data['nama'],
            'role_id'        => $data['role_id'],
            'logged_in'     => TRUE
          ];

          $getMenu = $model->getMenu($data['role_id']);
          $menu = array(
            'menus' => array(),
            'parent_menus' => array(),
          );
          foreach ($getMenu as $row) {
            $menu['menus'][$row->kode_menu] = $row;
            $menu['parent_menus'][$row->kode_induk][] = $row->kode_menu;
          }

          $ses_data['menu'] = $menu;

          $session->set($ses_data);

          // ambil parameter redirect dari POST
          $redirect = $this->request->getPost('redirect');
          if ($redirect && !empty($redirect)) {
            $redirectUrl = urldecode($redirect);
            if (strpos($redirectUrl, base_url()) === 0 || strpos($redirectUrl, '/') === 0) {
              return redirect()->to($redirectUrl);
            }
          }

          return redirect()->route('home');
        } else {
          $session->setFlashdata('userx', $username);
          $session->setFlashdata('error', '* Akun anda belum aktif!');
          return redirect()->route('login');
        }
      } else {
        $session->setFlashdata('userx', $username);
        $session->setFlashdata('error', '* Password Salah!');
        return redirect()->route('login');
      }
    } else {
      $session->setFlashdata('error', '* Username Salah!');
      return redirect()->route('login');
    }
  }

  public function register()
  {
    $session = session();
    if ($session->get('logged_in') == FALSE) {
      helper('form');
      return view('auth/v_register');
    } else {
      return redirect()->route('home');
    }
  }

  public function logout()
  {
    $session = session();
    $session->destroy();
    // return redirect()->route('login');
    return redirect()->route('/');
  }

  public function actRegister()
  {
    $session = session();
    $model = new AuthModel();

    // Ambil data dari form
    $name = $this->request->getPost('nama');
    $username = $this->request->getPost('usr');
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('pwd');
    $address = $this->request->getPost('address');

    // Validasi sederhana
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($address)) {
      $session->setFlashdata('msg', 'Semua field wajib diisi!');
      return redirect()->route('register');
    }

    // Cek username sudah ada
    if ($model->checkUsername($username) > 0) {
      $session->setFlashdata('msg', 'Username sudah terdaftar!');
      return redirect()->route('register');
    }

    // Cek email sudah ada
    if ($model->checkEmail($email) > 0) {
      $session->setFlashdata('msg', 'Email sudah terdaftar!');
      return redirect()->route('register');
    }

    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Siapkan data user baru
    $data = [
      'username' => $username,
      'password' => $hash,
      'nama' => $name,
      'email' => $email,
      'alamat' => $address,
      'status_user' => 1, // default belum aktif, bisa diubah sesuai kebutuhan
      'role_id' => 2, // default role user biasa, sesuaikan jika perlu
      'last_login' => null
    ];

    // Insert ke database
    try {
      $model->registerUser($data);
      $session->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
      return redirect()->route('login');
    } catch (\Exception $e) {
      $session->setFlashdata('error', 'Registrasi gagal: ' . $e->getMessage());
      return redirect()->route('register');
    }
  }

  // untuk fitur lupa password
  public function forgot()
  {
    $data = [
      'title' => 'Lupa Password',
    ];
    helper('form');
    return view('auth/v_lupa_password', $data);
  }

  public function sendReset()
  {
    $session = session();
    $email = $this->request->getPost('email');

    $model = new AuthModel();
    $user = $model->where('email', $email)->first();

    if (!$user) {
      $session->setFlashdata('error', '* Email Salah!');
      return redirect()->route('forgot');
    }

    helper('text');
    $token = random_string('alnum', 64);

    $modelResetPassword = new MyModel('password_resets');
    $data = [
      'user_id'    => $user['id_user'],
      'token'      => $token,
      'used'       => 0,
      'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
    ];
    $modelResetPassword->insertData($data);

    $resetLink = base_url('reset/' . $token);

    // Gunakan EmailService
    $emailService = new EmailServices();
    $message = "
        <p>Halo <strong>{$user['nama']}</strong>,</p>
        <p>Kami menerima permintaan untuk mereset password Anda.</p>
        <p>Klik link berikut untuk reset password:</p>
        <p><a href='{$resetLink}'>{$resetLink}</a></p>
        <p>Jika Anda tidak meminta ini, abaikan email ini.</p>
    ";

    // email user
    $model = new MyModel('konfigurasi');
    $get = $model->getDataById('id_konfigurasi', 1);

    $emailService->send([
      'from_email' => $get->email,     // bisa dinamis juga kalau mau
      'from_name'  => 'Notification',
      'to'         => $email,
      'subject'    => 'Reset Password Anda',
      'message'    => $message
    ]);

    return redirect()->back()->with('success', 'Link reset telah dikirim ke email Anda.');
  }

  public function resetPassword($token)
  {
    $model = new MyModel('password_resets');
    $where = [
      'token' => $token,
      'used' => 0,
      'expired_at >=' => date('Y-m-d H:i:s')
    ];
    $reset = $model->getDataByWhere($where);

    if (!$reset) {
      return redirect()->to('/login')->with('error', 'Token tidak valid atau telah kadaluarsa.');
    }
    helper('form');
    return view('auth/v_reset_password', ['token' => $token]);
  }

  public function sendPassword()
  {
    $token = $this->request->getPost('token');
    $password = $this->request->getPost('pass');
    $repass = $this->request->getPost('reppass');
    if ($password !== $repass) {
      return redirect()->back()->with('error', 'Password dan konfirmasi tidak sama.');
    }
    $modelReset = new MyModel('password_resets');
    $where = [
      'token' => $token,
      'used' => 0,
      'expired_at >=' => date('Y-m-d H:i:s')
    ];
    $reset = $modelReset->getDataByWhere($where);

    if (!$reset) {
      return redirect()->back()->with('error', 'Token tidak valid atau telah kadaluarsa.');
    }
    $data = [
      'password' => password_hash($password, PASSWORD_DEFAULT),
    ];
    $modelUser = new MyModel('users');
    $modelUser->updateData($data, 'id_user', $reset->user_id);

    $data = [
      'used' => 1
    ];
    $modelReset->updateData($data, 'user_id', $reset->user_id);

    return redirect()->to('login')->with('success', 'Password berhasil diubah');
  }
}
