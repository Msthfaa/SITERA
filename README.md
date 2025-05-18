# SITERA – Sistem Terpadu Rekapitulasi Akademik

SITERA adalah aplikasi **Node.js + Express.js** untuk membantu dosen dan admin kampus merekap dan mengelola nilai mahasiswa secara terpusat.

---

## Fitur Utama

* **Manajemen Data**
  Menyimpan **Mahasiswa, Mata Kuliah, Kelas, dan Jurusan** di MySQL dengan relasi yang jelas.

* **Input & Rekap Nilai**
  Form penilaian **UTS, UAS, dan Tugas** terintegrasi yang otomatis tersimpan di tabel `penilaian`.
  Rekap nilai per mahasiswa & per mata kuliah dapat diunduh atau ditampilkan langsung.

* **RESTful API**
  Endpoint CRUD lengkap untuk setiap entitas—siap dipakai front‑end mana pun atau Postman.

* **Autentikasi**
  Middleware **JWT** memastikan hanya pengguna terdaftar yang dapat mengakses dan mengubah data.

* **Modular Codebase**
  Struktur folder terpisah untuk **routes, controllers, models,** dan **views (EJS)** sehingga mudah dipelihara.

---

## Tech Stack

> Node.js · Express.js · MySQL · Sequelize · JWT · EJS · Bootstrap

---

## Manfaat

Dengan SITERA, proses penilaian menjadi lebih **cepat, akurat, dan terpusat**, mengurangi kerja manual serta meminimalisir kesalahan input.
