<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan error default yang digunakan oleh
    | kelas validator. Beberapa aturan memiliki beberapa versi seperti
    | aturan ukuran. Silakan sesuaikan pesan-pesan ini di sini.
    |
    */

    'accepted' => 'Kolom :attribute wajib disetujui.',
    'accepted_if' => 'Kolom :attribute wajib disetujui ketika :other adalah :value.',
    'active_url' => 'Kolom :attribute bukan URL yang valid.',
    'after' => 'Kolom :attribute wajib berupa tanggal setelah :date.',
    'after_or_equal' => 'Kolom :attribute wajib berupa tanggal setelah atau sama dengan :date.',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Kolom :attribute wajib berupa array.',
    'ascii' => 'Kolom :attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before' => 'Kolom :attribute wajib berupa tanggal sebelum :date.',
    'before_or_equal' => 'Kolom :attribute wajib berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => 'Kolom :attribute wajib memiliki antara :min dan :max item.',
        'file' => 'Kolom :attribute wajib antara :min dan :max kilobita.',
        'numeric' => 'Kolom :attribute wajib antara :min dan :max.',
        'string' => 'Kolom :attribute wajib antara :min dan :max karakter.',
    ],
    'boolean' => 'Kolom :attribute wajib bernilai benar atau salah.',
    'can' => 'Kolom :attribute berisi nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi kolom :attribute tidak cocok.',
    'contains' => 'Kolom :attribute kekurangan nilai yang diperlukan.',
    'current_password' => 'Password salah.',
    'date' => 'Kolom :attribute bukan tanggal yang valid.',
    'date_equals' => 'Kolom :attribute wajib berupa tanggal yang sama dengan :date.',
    'date_format' => 'Kolom :attribute tidak cocok dengan format :format.',
    'decimal' => 'Kolom :attribute wajib memiliki :decimal tempat desimal.',
    'declined' => 'Kolom :attribute wajib ditolak.',
    'declined_if' => 'Kolom :attribute wajib ditolak ketika :other adalah :value.',
    'different' => 'Kolom :attribute dan :other wajib berbeda.',
    'digits' => 'Kolom :attribute wajib terdiri dari :digits digit.',
    'digits_between' => 'Kolom :attribute wajib antara :min dan :max digit.',
    'dimensions' => 'Kolom :attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => 'Kolom :attribute memiliki nilai yang duplikat.',
    'doesnt_end_with' => 'Kolom :attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with' => 'Kolom :attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email' => 'Kolom :attribute wajib berupa alamat email yang valid.',
    'ends_with' => 'Kolom :attribute wajib diakhiri dengan salah satu dari: :values.',
    'enum' => 'Nilai :attribute yang dipilih tidak valid.',
    'exists' => 'Nilai :attribute yang dipilih tidak valid.',
    'extensions' => 'Kolom :attribute wajib memiliki salah satu ekstensi berikut: :values.',
    'file' => 'Kolom :attribute wajib berupa file.',
    'filled' => 'Kolom :attribute wajib memiliki nilai.',
    'gt' => [
        'array' => 'Kolom :attribute wajib memiliki lebih dari :value item.',
        'file' => 'Kolom :attribute wajib lebih besar dari :value kilobita.',
        'numeric' => 'Kolom :attribute wajib lebih besar dari :value.',
        'string' => 'Kolom :attribute wajib lebih panjang dari :value karakter.',
    ],
    'gte' => [
        'array' => 'Kolom :attribute wajib memiliki minimal :value item.',
        'file' => 'Kolom :attribute wajib lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => 'Kolom :attribute wajib lebih besar dari atau sama dengan :value.',
        'string' => 'Kolom :attribute wajib lebih panjang dari atau sama dengan :value karakter.',
    ],
    'hex_color' => 'Kolom :attribute wajib berupa warna heksadesimal yang valid.',
    'image' => 'Kolom :attribute wajib berupa gambar.',
    'in' => 'Nilai :attribute yang dipilih tidak valid.',
    'in_array' => 'Kolom :attribute tidak ada di dalam :other.',
    'integer' => 'Kolom :attribute wajib berupa bilangan bulat.',
    'ip' => 'Kolom :attribute wajib berupa alamat IP yang valid.',
    'ipv4' => 'Kolom :attribute wajib berupa alamat IPv4 yang valid.',
    'ipv6' => 'Kolom :attribute wajib berupa alamat IPv6 yang valid.',
    'json' => 'Kolom :attribute wajib berupa string JSON yang valid.',
    'list' => 'Kolom :attribute wajib berupa daftar.',
    'lowercase' => 'Kolom :attribute wajib berupa huruf kecil.',
    'lt' => [
        'array' => 'Kolom :attribute wajib memiliki kurang dari :value item.',
        'file' => 'Kolom :attribute wajib lebih kecil dari :value kilobita.',
        'numeric' => 'Kolom :attribute wajib lebih kecil dari :value.',
        'string' => 'Kolom :attribute wajib lebih pendek dari :value karakter.',
    ],
    'lte' => [
        'array' => 'Kolom :attribute tidak boleh memiliki lebih dari :value item.',
        'file' => 'Kolom :attribute wajib lebih kecil dari atau sama dengan :value kilobita.',
        'numeric' => 'Kolom :attribute wajib lebih kecil dari atau sama dengan :value.',
        'string' => 'Kolom :attribute wajib lebih pendek dari atau sama dengan :value karakter.',
    ],
    'mac_address' => 'Kolom :attribute wajib berupa alamat MAC yang valid.',
    'max' => [
        'array' => 'Kolom :attribute tidak boleh memiliki lebih dari :max item.',
        'file' => 'Kolom :attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => 'Kolom :attribute tidak boleh lebih besar dari :max.',
        'string' => 'Kolom :attribute tidak boleh lebih panjang dari :max karakter.',
    ],
    'max_digits' => 'Kolom :attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => 'Kolom :attribute wajib berupa file berjenis: :values.',
    'mimetypes' => 'Kolom :attribute wajib berupa file berjenis: :values.',
    'min' => [
        'array' => 'Kolom :attribute wajib memiliki minimal :min item.',
        'file' => 'Kolom :attribute wajib berukuran minimal :min kilobita.',
        'numeric' => 'Kolom :attribute wajib bernilai minimal :min.',
        'string' => 'Kolom :attribute wajib berukuran minimal :min karakter.',
    ],
    'min_digits' => 'Kolom :attribute wajib memiliki minimal :min digit.',
    'missing' => 'Kolom :attribute wajib hilang.',
    'missing_if' => 'Kolom :attribute wajib hilang ketika :other adalah :value.',
    'missing_unless' => 'Kolom :attribute wajib hilang kecuali :other ada di :values.',
    'missing_with' => 'Kolom :attribute wajib hilang ketika :values ada.',
    'missing_with_all' => 'Kolom :attribute wajib hilang ketika semua :values ada.',
    'multiple_of' => 'Kolom :attribute wajib merupakan kelipatan dari :value.',
    'not_in' => 'Nilai :attribute yang dipilih tidak valid.',
    'not_regex' => 'Format kolom :attribute tidak valid.',
    'numeric' => 'Kolom :attribute wajib berupa angka.',
    'password' => [
        'letters' => 'Kolom :attribute wajib berisi minimal satu huruf.',
        'mixed' => 'Kolom :attribute wajib berisi minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => 'Kolom :attribute wajib berisi minimal satu angka.',
        'symbols' => 'Kolom :attribute wajib berisi minimal satu simbol.',
        'uncompromised' => 'Nilai :attribute yang diberikan telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present' => 'Kolom :attribute wajib ada.',
    'present_if' => 'Kolom :attribute wajib ada ketika :other adalah :value.',
    'present_unless' => 'Kolom :attribute wajib ada kecuali :other adalah :value.',
    'present_with' => 'Kolom :attribute wajib ada ketika :values ada.',
    'present_with_all' => 'Kolom :attribute wajib ada ketika semua :values ada.',
    'prohibited' => 'Kolom :attribute dilarang.',
    'prohibited_if' => 'Kolom :attribute dilarang ketika :other adalah :value.',
    'prohibited_unless' => 'Kolom :attribute dilarang kecuali :other ada di :values.',
    'prohibits' => 'Kolom :attribute melarang :other hadir.',
    'regex' => 'Format kolom :attribute tidak valid.',
    'required' => 'Kolom :attribute wajib diisi.',
    'required_array_keys' => 'Kolom :attribute wajib berisi entri untuk: :values.',
    'required_if' => 'Kolom :attribute wajib diisi ketika :other adalah :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi ketika :other disetujui.',
    'required_if_declined' => 'Kolom :attribute wajib diisi ketika :other ditolak.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other ada di :values.',
    'required_with' => 'Kolom :attribute wajib diisi ketika :values ada.',
    'required_with_all' => 'Kolom :attribute wajib diisi ketika semua :values ada.',
    'required_without' => 'Kolom :attribute wajib diisi ketika :values tidak ada.',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika tidak ada satu pun :values yang ada.',
    'same' => 'Kolom :attribute dan :other wajib cocok.',
    'size' => [
        'array' => 'Kolom :attribute wajib berisi :size item.',
        'file' => 'Kolom :attribute wajib berukuran :size kilobita.',
        'numeric' => 'Kolom :attribute wajib bernilai :size.',
        'string' => 'Kolom :attribute wajib berukuran :size karakter.',
    ],
    'starts_with' => 'Kolom :attribute wajib diawali dengan salah satu dari: :values.',
    'string' => 'Kolom :attribute wajib berupa string/teks.',
    'timezone' => 'Kolom :attribute wajib berupa zona waktu yang valid.',
    'unique' => ':attribute sudah terdaftar.',
    'uploaded' => 'Kolom :attribute gagal diunggah.',
    'uppercase' => 'Kolom :attribute wajib berupa huruf besar.',
    'url' => 'Kolom :attribute wajib berupa URL yang valid.',
    'ulid' => 'Kolom :attribute wajib berupa ULID yang valid.',
    'uuid' => 'Kolom :attribute wajib berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi Kustom
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atribut Validasi Kustom
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'Nama Lengkap',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Konfirmasi Password',
        'phone' => 'Nomor Telepon',
        'quantity' => 'Jumlah Porsi',
        'notes' => 'Catatan',
        'address' => 'Alamat',
    ],

];
