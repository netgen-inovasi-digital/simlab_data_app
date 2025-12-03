# NetX Template

**NetX Template** adalah template website modern berbasis PHP CodeIgniter 4 yang siap digunakan untuk berbagai kebutuhan web development. Template ini dilengkapi dengan fitur manajemen konten, sistem konfigurasi profil, dan interface admin yang user-friendly.

## ✨ Fitur Utama

- 🎨 **Template Website Modern** - Design responsif dengan Bootstrap 5
- ⚙️ **Sistem Konfigurasi Profil** - Upload logo, manajemen informasi profil lengkap
- 📄 **Manajemen Halaman** - CRUD halaman dan konten dinamis
- 🔐 **Sistem Autentikasi** - Login/logout terintegrasi
- 🗺️ **Integrasi Peta** - Embed Google Maps untuk lokasi
- 📱 **Responsive Design** - Compatible dengan semua perangkat
- 🔧 **Easy Configuration** - Setup mudah dengan file .env

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.x, CodeIgniter 4
- **Frontend**: Bootstrap 5, JavaScript
- **Database**: MySQL/MariaDB
- **Icons**: Bootstrap Icons
- **Fonts**: Google Fonts (Inter, Quicksand)

## 🚀 Cara Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/yourusername/netx.git
   cd netx
   ```

2. **Tidak perlu install Composer**  
   Semua dependensi sudah tersedia di repository.

3. **Setup Server & Database**
   - Nyalakan Apache/Nginx dan MySQL (gunakan XAMPP/WAMP/LARAGON)
   - Buat database baru untuk project

4. **Konfigurasi Environment**
   - Salin file `.env.example` menjadi `.env`
   - Edit konfigurasi database di file `.env`:
   ```
   database.default.database = nama_database_anda
   database.default.username = username_database
   database.default.password = password_database
   ```

5. **Arahkan Server**
   - Pastikan server mengarah ke folder project
   - Akses melalui browser: `http://localhost/netx/`

## 📁 Struktur Project

```
netx/
├── appengines/
│   ├── app/
│   │   ├── Controllers/
│   │   ├── Models/
│   │   ├── Views/
│   │   ├── Modules/
│   │   │   └── Konfigurasi/
│   │   └── ...
│   |── writable/
|   ├── .env
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── uploads/
├── .gitignore
├── README.md
└── Guidelines.md
```

## 🧾 Panduan Development

### Format Commit
```bash
[type]: [deskripsi singkat]

# Contoh:
feat: menambahkan fitur upload logo profil
fix: memperbaiki bug responsive navbar
docs: update dokumentasi installation
```

**Jenis Commit:**
- `feat` → penambahan fitur baru
- `fix` → perbaikan bug
- `docs` → perubahan dokumentasi
- `refactor` → refactoring kode

### Aturan Branching
| Branch | Tujuan | Contoh |
|--------|--------|--------|
| `main` | Branch stabil untuk production | - |
| `dev` | Branch utama development | - |
| `feature/*` | Fitur baru | `feature/user-management` |
| `fix/*` | Perbaikan bug | `fix/navbar-responsive` |
| `docs/*` | Update dokumentasi | `docs/api-documentation` |

## 🤝 Contributing

1. Fork repository
2. Buat branch feature (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'feat: add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## 📝 License

Distributed under the MIT License. See `LICENSE` for more information.

## 👥 Tim Pengembang

- **Developer** - [@netgen-inovasi-digital](https://github.com/netgen-inovasi-digital)

## 📞 Kontak & Support

- **Email**: your.email@example.com
- **GitHub Issues**: [Issues](https://github.com/netgen-inovasi-digital/netx-template/issues)
- **Documentation**: [Wiki](https://github.com/netgen-inovasi-digital/netx-template/wiki)

---


⭐ **Jangan lupa berikan star jika project ini