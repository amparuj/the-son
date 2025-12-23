{{-- เพิ่มเมนู Options ใน navbar staff (นำไป merge ใน layouts.staff ของคุณ) --}}
<a class="nav-link {{ request()->routeIs('staff.options.*') ? 'active' : '' }}"
   href="{{ route('staff.options.index') }}">
  Options
</a>
