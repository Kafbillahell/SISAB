<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/') }}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SISAB</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Master Data
    </div>

    <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>Manajemen User</span></a>
    </li>

    <li class="nav-item">
    <a class="nav-link {{ request()->is('tahun-ajaran*', 'jurusan*', 'kelas*', 'mapel*', 'rombel*', 'anggota-rombel*', 'sesi*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseAkademik"
        aria-expanded="true" aria-controls="collapseAkademik">
        <i class="fas fa-fw fa-graduation-cap"></i>
        <span>Data Akademik</span>
    </a>
    
    <div id="collapseAkademik" class="collapse {{ request()->is('tahun-ajaran*', 'jurusan*', 'kelas*', 'mapel*', 'rombel*', 'anggota-rombel*', 'sesi*') ? 'show' : '' }}" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item {{ request()->is('tahun-ajaran*') ? 'active' : '' }}" href="{{ route('tahun-ajaran.index') }}">Tahun Ajaran</a>
            
            <a class="collapse-item {{ request()->is('jurusan*') ? 'active' : '' }}" href="{{ route('jurusan.index') }}">Jurusan</a>
            
            <a class="collapse-item {{ request()->is('kelas*') ? 'active' : '' }}" href="{{ route('kelas.index') }}">Kelas</a>

            <a class="collapse-item {{ request()->is('sesi*') ? 'active' : '' }}" href="{{ route('sesi.index') }}">Modul Waktu</a>

            <a class="collapse-item {{ request()->is('mapel*') ? 'active' : '' }}" href="{{ route('mapel.index') }}">Mata Pelajaran</a>
            <a class="collapse-item {{ request()->is('rombel*') ? 'active' : '' }}" href="{{ route('rombel.index') }}">Rombongan Belajar</a>
            <a class="collapse-item {{ request()->is('anggota-rombel*') ? 'active' : '' }}" href="{{ route('anggota-rombel.index') }}">Anggota Rombel</a>
        </div>
    </div>
</li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Personalia
    </div>

    <li class="nav-item {{ request()->is('guru*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('guru.index') }}">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Data Guru</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('siswas*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('siswas.index') }}">
            <i class="fas fa-fw fa-user-graduate"></i>
            <span>Data Siswa</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Absensi & Jadwal
    </div>

    <li class="nav-item {{ request()->is('presensi/scanner*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('presensi.scanner') }}">
            <i class="fas fa-fw fa-camera"></i>
            <span>Presensi Wajah</span></a>
    </li>

    <li class="nav-item {{ request()->is('jadwal*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('jadwal.index') }}">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Jadwal Pelajaran</span></a>
    </li>

    <li class="nav-item {{ request()->is('presensi') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('presensi.index') }}">
            <i class="fas fa-fw fa-clipboard-check"></i>
            <span>Rekap Absensi</span></a>
    </li>

    <li class="nav-item {{ request()->is('presensi/manual*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('presensi.manual') }}">
            <i class="fas fa-fw fa-edit"></i>
            <span>Input Manual</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Poin & Reward
    </div>

    <li class="nav-item {{ request()->is('my-points*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('points.myPoints') }}">
            <i class="fas fa-fw fa-trophy"></i>
            <span>Poin Saya</span></a>
    </li>

    <li class="nav-item {{ request()->is('vouchers*', 'my-vouchers*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->is('vouchers*', 'my-vouchers*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseVoucher"
            aria-expanded="true" aria-controls="collapseVoucher">
            <i class="fas fa-fw fa-gift"></i>
            <span>Voucher</span>
        </a>
        
        <div id="collapseVoucher" class="collapse {{ request()->is('vouchers*', 'my-vouchers*') ? 'show' : '' }}" aria-labelledby="headingVoucher" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('vouchers') && !request()->is('my-vouchers*') ? 'active' : '' }}" href="{{ route('vouchers.index') }}">Tukar Voucher</a>
                <a class="collapse-item {{ request()->is('my-vouchers*') ? 'active' : '' }}" href="{{ route('vouchers.myVouchers') }}">Voucher Saya</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Management
    </div>

    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'guru']))
    <li class="nav-item {{ request()->is('penilaian-sikap*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('penilaian-sikap.index') }}">
            <i class="fas fa-fw fa-star"></i>
            <span>Penilaian Sikap Siswa</span></a>
    </li>
    @endif

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Pengaturan
    </div>

    <li class="nav-item {{ request()->is('admin/vouchers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.vouchers.index') }}">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Manajemen Voucher</span></a>
    </li>

    <li class="nav-item {{ request()->is('periode*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('periode.index') }}">
            <i class="fas fa-fw fa-clock"></i>
            <span>Atur Periode</span></a>
    </li>

    <li class="nav-item {{ request()->is('settings/lokasi*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('settings.lokasi') }}">
            <i class="fas fa-fw fa-map-marker-alt"></i>
            <span>Atur Lokasi GPS</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>