{{-- เพิ่มเมนู Option Groups ใน navbar staff --}}
<a class="nav-link {{ request()->routeIs('staff.option-groups.*') ? 'active' : '' }}"
   href="{{ route('staff.option-groups.index') }}">
  Option Groups
</a>
