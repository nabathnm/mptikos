<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $passwordHash = Hash::make('password'); // Password default semua akun baru: 'password'

        // 1. TAMBAH 3 PEMILIK
        $pemiliks = [
            [
                'nama' => 'Budi Santoso',
                'email' => 'pemilik.budi@gmail.com',
                'role' => 'pemilik',
                'no_telepon' => '081234567801',
                'nama_bank' => 'BCA',
                'nomor_rekening' => '8830192831',
                'nama_rekening' => 'Budi Santoso',
            ],
            [
                'nama' => 'Siti Rahmawati',
                'email' => 'pemilik.siti@gmail.com',
                'role' => 'pemilik',
                'no_telepon' => '081234567802',
                'nama_bank' => 'Mandiri',
                'nomor_rekening' => '1420019283741',
                'nama_rekening' => 'Siti Rahmawati',
            ],
            [
                'nama' => 'Hendro Wijaya',
                'email' => 'pemilik.hendro@gmail.com',
                'role' => 'pemilik',
                'no_telepon' => '081234567803',
                'nama_bank' => 'BRI',
                'nomor_rekening' => '012301092837502',
                'nama_rekening' => 'Hendro Wijaya',
            ],
        ];

        $pemilikIds = [];
        foreach ($pemiliks as $p) {
            $existing = DB::table('users')->where('email', $p['email'])->value('id');
            if ($existing) {
                $pemilikIds[$p['email']] = $existing;
            } else {
                $pemilikIds[$p['email']] = DB::table('users')->insertGetId([
                    'nama' => $p['nama'],
                    'email' => $p['email'],
                    'password' => $passwordHash,
                    'role' => $p['role'],
                    'no_telepon' => $p['no_telepon'],
                    'nama_bank' => $p['nama_bank'],
                    'nomor_rekening' => $p['nomor_rekening'],
                    'nama_rekening' => $p['nama_rekening'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 2. TAMBAH 6 PENCARI KOS (role: user)
        $pencaris = [
            ['nama' => 'Rian Pratama', 'email' => 'rian.pratama@gmail.com', 'no_telepon' => '082198765401'],
            ['nama' => 'Dinda Lestari', 'email' => 'dinda.lestari@gmail.com', 'no_telepon' => '082198765402'],
            ['nama' => 'Ahmad Fauzi', 'email' => 'ahmad.fauzi@gmail.com', 'no_telepon' => '082198765403'],
            ['nama' => 'Maya Putri', 'email' => 'maya.putri@gmail.com', 'no_telepon' => '082198765404'],
            ['nama' => 'Dimas Anggara', 'email' => 'dimas.anggara@gmail.com', 'no_telepon' => '082198765405'],
            ['nama' => 'Anisa Zahra', 'email' => 'anisa.zahra@gmail.com', 'no_telepon' => '082198765406'],
        ];

        $pencariIds = [];
        foreach ($pencaris as $p) {
            $existing = DB::table('users')->where('email', $p['email'])->value('id');
            if ($existing) {
                $pencariIds[$p['email']] = $existing;
            } else {
                $pencariIds[$p['email']] = DB::table('users')->insertGetId([
                    'nama' => $p['nama'],
                    'email' => $p['email'],
                    'password' => $passwordHash,
                    'role' => 'user',
                    'no_telepon' => $p['no_telepon'],
                    'nama_bank' => null,
                    'nomor_rekening' => null,
                    'nama_rekening' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. TAMBAH KOSAN (Masing-masing pemilik memiliki 2 kosan = total 6 kosan)
        $kosans = [
            // Milik Budi
            [
                'user_id' => $pemilikIds['pemilik.budi@gmail.com'],
                'nama_kosan' => 'Kosan Griya Merbabu',
                'alamat' => 'Jl. Merbabu No. 12',
                'deskripsi' => 'Kosan putra strategis dekat kampus, suasana tenang dan asri.',
                'kota' => 'Malang',
                'harga_per_bulan' => 850000,
                'jumlah_kamar' => 8,
                'kamar_tersedia' => 6,
                'tipe' => 'putra',
                'fasilitas' => json_encode(['WiFi', 'Kasur', 'Lemari', 'Parkir Motor']),
                'foto' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'user_id' => $pemilikIds['pemilik.budi@gmail.com'],
                'nama_kosan' => 'Kost Harmoni Residence',
                'alamat' => 'Jl. Manyar Kertoarjo No. 45',
                'deskripsi' => 'Kost modern fasilitas premium, parkir luas dan security 24 jam.',
                'kota' => 'Surabaya',
                'harga_per_bulan' => 1500000,
                'jumlah_kamar' => 12,
                'kamar_tersedia' => 8,
                'tipe' => 'campur',
                'fasilitas' => json_encode(['WiFi', 'AC', 'Kamar Mandi Dalam', 'Parkir Mobil', 'Water Heater']),
                'foto' => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
            ],
            // Milik Siti
            [
                'user_id' => $pemilikIds['pemilik.siti@gmail.com'],
                'nama_kosan' => 'Kost Putri Melati Indah',
                'alamat' => 'Jl. Sigura-gura Barat Kav. 3',
                'deskripsi' => 'Khusus putri, aman, bersih, dan dekat pusat kuliner.',
                'kota' => 'Malang',
                'harga_per_bulan' => 900000,
                'jumlah_kamar' => 10,
                'kamar_tersedia' => 7,
                'tipe' => 'putri',
                'fasilitas' => json_encode(['WiFi', 'Dapur Bersama', 'Kulkas', 'Kasur', 'CCTV']),
                'foto' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'user_id' => $pemilikIds['pemilik.siti@gmail.com'],
                'nama_kosan' => 'Griya Putri Kartini',
                'alamat' => 'Jl. Kaliurang Km 5',
                'deskripsi' => 'Kost putri eksklusif, lingkungan nyaman cocok untuk belajar.',
                'kota' => 'Yogyakarta',
                'harga_per_bulan' => 1200000,
                'jumlah_kamar' => 6,
                'kamar_tersedia' => 3,
                'tipe' => 'putri',
                'fasilitas' => json_encode(['WiFi', 'AC', 'Kamar Mandi Dalam', 'Meja Belajar']),
                'foto' => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
            ],
            // Milik Hendro
            [
                'user_id' => $pemilikIds['pemilik.hendro@gmail.com'],
                'nama_kosan' => 'Kost Eksekutif Brawijaya',
                'alamat' => 'Jl. Veteran No. 88',
                'deskripsi' => 'Kost eksekutif luxury view kota, fasilitas hotel bintang 3.',
                'kota' => 'Malang',
                'harga_per_bulan' => 2200000,
                'jumlah_kamar' => 15,
                'kamar_tersedia' => 10,
                'tipe' => 'campur',
                'fasilitas' => json_encode(['WiFi', 'AC', 'Water Heater', 'TV', 'Parkir Mobil', 'Cleaning Service']),
                'foto' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'user_id' => $pemilikIds['pemilik.hendro@gmail.com'],
                'nama_kosan' => 'Kost Paviliun Cendana',
                'alamat' => 'Jl. Dago Asri No. 17',
                'deskripsi' => 'Kost paviliun sejuk di kawasan Dago Bandung.',
                'kota' => 'Bandung',
                'harga_per_bulan' => 1100000,
                'jumlah_kamar' => 8,
                'kamar_tersedia' => 5,
                'tipe' => 'putra',
                'fasilitas' => json_encode(['WiFi', 'Kamar Mandi Dalam', 'Kasur', 'Dapur Bersama']),
                'foto' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $kosanIds = [];
        foreach ($kosans as $k) {
            $foto = $k['foto'];
            unset($k['foto']);

            $existingKosan = DB::table('kosans')->where('nama_kosan', $k['nama_kosan'])->value('id');
            if ($existingKosan) {
                $kId = $existingKosan;
            } else {
                $kId = DB::table('kosans')->insertGetId(array_merge($k, [
                    'status' => 'aktif',
                    'nama_bank' => null,
                    'nomor_rekening' => null,
                    'nama_rekening' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));

                // Foto utama kosan
                DB::table('foto_kosans')->insert([
                    'kosan_id' => $kId,
                    'foto' => $foto,
                    'is_utama' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Kamar per kosan
                for ($i = 1; $i <= 3; $i++) {
                    DB::table('kamar')->insert([
                        'kosan_id' => $kId,
                        'nama_kamar' => 'Kamar ' . (100 + $i),
                        'harga' => $k['harga_per_bulan'],
                        'fasilitas' => 'Kasur, Lemari, Meja Belajar',
                        'status' => $i === 1 ? 'terisi' : 'kosong',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
            $kosanIds[$k['nama_kosan']] = $kId;
        }

        // 4. TAMBAH PEMESANAN DENGAN BERBAGAI STATUS
        $pemesanans = [
            // 1) Status pending (Rian memesan Griya Merbabu, bukti transfer terunggah)
            [
                'user_id' => $pencariIds['rian.pratama@gmail.com'],
                'kosan_id' => $kosanIds['Kosan Griya Merbabu'],
                'tanggal_masuk' => Carbon::now()->addDays(3)->toDateString(),
                'durasi_bulan' => 3,
                'total_harga' => 850000 * 3,
                'status' => 'pending',
                'catatan' => 'Rencana check-in sore hari sekitar jam 4.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ],
            // 2) Status disetujui (Dinda memesan Melati Indah - SUDAH ADA ulasan awal)
            [
                'user_id' => $pencariIds['dinda.lestari@gmail.com'],
                'kosan_id' => $kosanIds['Kost Putri Melati Indah'],
                'tanggal_masuk' => Carbon::now()->addDays(1)->toDateString(),
                'durasi_bulan' => 6,
                'total_harga' => 900000 * 6,
                'status' => 'disetujui',
                'catatan' => 'Sudah lunas transfer 6 bulan. Mohon disiapkan kuncinya.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ],
            // 3) Status pending belum bayar (Ahmad memesan Harmoni Residence)
            [
                'user_id' => $pencariIds['ahmad.fauzi@gmail.com'],
                'kosan_id' => $kosanIds['Kost Harmoni Residence'],
                'tanggal_masuk' => Carbon::now()->addDays(7)->toDateString(),
                'durasi_bulan' => 1,
                'total_harga' => 1500000 * 1,
                'status' => 'pending',
                'catatan' => 'Akan transfer malam ini setelah pulang kerja.',
                'bukti_pembayaran' => null,
            ],
            // 4) Status ditolak (Maya memesan Griya Kartini)
            [
                'user_id' => $pencariIds['maya.putri@gmail.com'],
                'kosan_id' => $kosanIds['Griya Putri Kartini'],
                'tanggal_masuk' => Carbon::now()->subDays(2)->toDateString(),
                'durasi_bulan' => 2,
                'total_harga' => 1200000 * 2,
                'status' => 'ditolak',
                'catatan' => 'Ingin sewa untuk 2 bulan.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ],
            // 5) Status disetujui (Dimas memesan Kost Eksekutif Brawijaya - BELUM ULASAN: BISA DIGUNAKAN UNTUK TEST FORM INSERT ULASAN)
            [
                'user_id' => $pencariIds['dimas.anggara@gmail.com'],
                'kosan_id' => $kosanIds['Kost Eksekutif Brawijaya'],
                'tanggal_masuk' => Carbon::now()->addDays(5)->toDateString(),
                'durasi_bulan' => 12,
                'total_harga' => 2200000 * 12,
                'status' => 'disetujui',
                'catatan' => 'Sewa tahunan, pembayaran transfer penuh sudah terkonfirmasi.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ],
            // 6) Status disetujui (Anisa memesan Kost Paviliun Cendana - SUDAH ADA ulasan awal)
            [
                'user_id' => $pencariIds['anisa.zahra@gmail.com'],
                'kosan_id' => $kosanIds['Kost Paviliun Cendana'],
                'tanggal_masuk' => Carbon::now()->subMonths(1)->toDateString(),
                'durasi_bulan' => 1,
                'total_harga' => 1100000 * 1,
                'status' => 'disetujui',
                'catatan' => 'Masa sewa 1 bulan.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ],
        ];

        // Jika user pencari1@gmail.com (Nabath) ada, tambahkan juga pesanan disetujui di Kost Harmoni Residence agar akun yang sedang aktif login bisa langsung test ulasan tanpa logout
        $existingNabath = DB::table('users')->where('email', 'pencari1@gmail.com')->value('id');
        if ($existingNabath) {
            $pemesanans[] = [
                'user_id' => $existingNabath,
                'kosan_id' => $kosanIds['Kost Harmoni Residence'],
                'tanggal_masuk' => Carbon::now()->subDays(5)->toDateString(),
                'durasi_bulan' => 1,
                'total_harga' => 1500000,
                'status' => 'disetujui',
                'catatan' => 'Pesanan aktif disetujui untuk pengujian fitur ulasan.',
                'bukti_pembayaran' => 'https://res.cloudinary.com/l5erykgr/image/upload/v1793277115/mptikos/bukti_pembayaran/j1hgfkjc3wyi6zvmi8aq.png',
            ];
        }

        foreach ($pemesanans as $p) {
            $existingPemesanan = DB::table('pemesanans')
                ->where('user_id', $p['user_id'])
                ->where('kosan_id', $p['kosan_id'])
                ->first();

            if (!$existingPemesanan) {
                DB::table('pemesanans')->insert(array_merge($p, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        // 5. TAMBAH ULASAN DARI USER YANG STATUS PESANANNYA SUDAH DISETUJUI
        $ulasans = [
            [
                'user_id' => $pencariIds['dinda.lestari@gmail.com'],
                'kosan_id' => $kosanIds['Kost Putri Melati Indah'],
                'rating' => 5,
                'komentar' => 'Kost sangat nyaman, bersih, dan lingkungan ramah. Ibu kost juga sangat responsif dan baik hati!',
            ],
            [
                'user_id' => $pencariIds['anisa.zahra@gmail.com'],
                'kosan_id' => $kosanIds['Kost Paviliun Cendana'],
                'rating' => 4,
                'komentar' => 'Suasana sejuk di Bandung, kamar mandi bersih dan air panas lancar. Sangat recommended untuk mahasiswa!',
            ],
        ];

        foreach ($ulasans as $u) {
            $sudahAdaUlasan = DB::table('ulasans')
                ->where('user_id', $u['user_id'])
                ->where('kosan_id', $u['kosan_id'])
                ->exists();

            if (!$sudahAdaUlasan) {
                DB::table('ulasans')->insert(array_merge($u, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
