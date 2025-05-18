# SITERA – Sistem Terpadu Rekapitulasi Akademik

SITERA adalah aplikasi **PHP Native** yang membantu dosen dan admin kampus merekap serta mengelola nilai mahasiswa secara terpusat.

---

## Fitur Utama

* **Manajemen Data**
  Menyimpan **Mahasiswa, Mata Kuliah, Kelas, dan Jurusan** di **MySQL** dengan relasi yang jelas.

* **Input & Rekap Nilai**
  Form penilaian **UTS, UAS, dan Tugas** terintegrasi yang otomatis tersimpan di tabel `penilaian`.
  Rekap nilai per mahasiswa & per mata kuliah dapat diunduh (CSV/PDF) atau ditampilkan langsung.

* **RESTful‑like Endpoint**
  Skrip PHP terpisah untuk operasi CRUD masing‑masing entitas—mudah di‑integrasikan dengan Postman atau front‑end lain.

* **Autentikasi Sederhana**
  Sistem login berbasis session memastikan hanya pengguna terdaftar yang dapat mengakses dan memodifikasi data.

* **Modular Codebase**
  Struktur folder ringkas (config, models, controllers, views) yang memudahkan pemeliharaan dan pengembangan.

---

## Tech Stack

> PHP Native · MySQL · Bootstrap 5 · JavaScript (vanilla)

---

## Manfaat

Dengan SITERA, proses penilaian menjadi lebih **cepat, akurat, dan terpusat**, mengurangi kerja manual serta meminimalisir kesalahan input.
