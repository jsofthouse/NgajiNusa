<div class="section-tabs" id="sectionTabs">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
    <a href="{{ route('admin.murid.index') }}" class="{{ request()->routeIs('admin.murid.*') ? 'active' : '' }}">👨‍🎓 Murid</a>
    <a href="{{ route('admin.guru') }}" class="{{ request()->routeIs('admin.guru') ? 'active' : '' }}">👨‍🏫 Guru</a>
    <a href="{{ route('admin.jadwal') }}" class="{{ request()->routeIs('admin.jadwal') ? 'active' : '' }}">📅 Jadwal</a>
    <a href="{{ route('admin.transaksi.index') }}" class="{{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">💳 Transaksi</a>
    <a href="{{ route('admin.paket') }}" class="{{ request()->routeIs('admin.paket') ? 'active' : '' }}">🏷️ Paket</a>
</div>
