@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0; font-size: 24px; color: #222; margin-bottom: 24px;">Dashboard Overview</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(46,125,50,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" style="margin-bottom: 8px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Total Users</h3>
            <h1 style="color: #2e7d32; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['users'] }}</h1>
            @if($stats['pending_users'] > 0)
                <span class="status-badge" style="background: #ffc107; color: black; font-size: 11px;">{{ $stats['pending_users'] }} pending</span>
            @endif
        </div>

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(229,57,53,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e53935" stroke-width="2" style="margin-bottom: 8px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Lost Items</h3>
            <h1 style="color: #e53935; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['lost_items'] }}</h1>
            <small style="color: #999;">{{ $stats['resolved_lost'] }} Resolved</small>
        </div>

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(46,125,50,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#43a047" stroke-width="2" style="margin-bottom: 8px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Found Items</h3>
            <h1 style="color: #43a047; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['found_items'] }}</h1>
            <small style="color: #999;">{{ $stats['resolved_found'] }} Resolved</small>
        </div>

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(249,168,37,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f9a825" stroke-width="2" style="margin-bottom: 8px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Pending Claims</h3>
            <h1 style="color: #f9a825; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['pending_claims'] }}</h1>
            @if($stats['pending_claims'] > 0)
                <a href="{{ route('admin.claims') }}" style="font-size: 12px; color: #f9a825; text-decoration: underline;">Lihat Klaim →</a>
            @endif
        </div>

    </div>

    <!-- Student Data with Class Filter -->
    <div class="card" style="margin-top: 10px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
            <h2 style="font-size: 18px; color: #222; margin: 0;">📋 Data Siswa</h2>
            <form action="{{ route('admin.dashboard') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
                <select name="kelas" style="padding: 8px 14px; border-radius: 10px; border: 1px solid #ddd; font-family: 'Poppins', sans-serif; font-size: 13px; cursor: pointer;">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas }}" {{ $kelasFilter == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-approve" style="padding: 8px 16px;">Filter</button>
                @if($kelasFilter)
                    <a href="{{ route('admin.dashboard') }}" style="font-size: 12px; color: #e53935; text-decoration: none;">✕ Reset</a>
                @endif
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Photo</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            @if($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;">
                            @else
                                <div style="width: 36px; height: 36px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #888;">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            @endif
                        </td>
                        <td>{{ $student->nisn }}</td>
                        <td><strong>{{ $student->name }}</strong></td>
                        <td><span class="status-badge" style="background: #6c757d;">{{ $student->kelas ?? '-' }}</span></td>
                        <td>
                            @if($student->registration_status === 'approved')
                                <span class="status-badge" style="background: #28a745;">Approved</span>
                            @elseif($student->registration_status === 'pending')
                                <span class="status-badge" style="background: #ffc107; color: black;">Pending</span>
                            @else
                                <span class="status-badge" style="background: #dc3545;">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999; padding: 30px;">Tidak ada data siswa{{ $kelasFilter ? ' untuk kelas ' . $kelasFilter : '' }}.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 12px; font-size: 12px; color: #888;">
            Total: {{ $students->count() }} siswa{{ $kelasFilter ? ' (Kelas: ' . $kelasFilter . ')' : '' }}
        </div>
    </div>
@endsection
