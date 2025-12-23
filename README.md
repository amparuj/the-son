# Staff CRUD: Option Groups (ครบระบบ)

เพิ่มหน้า CRUD สำหรับ Option Group และเลือก Options เข้า Group ได้ (many-to-many ผ่าน option_group_items)

## ติดตั้ง
1) วาง Controller:
   app/Http/Controllers/Staff/OptionGroupController.php

2) วาง Views:
   resources/views/staff/option_groups/index.blade.php
   resources/views/staff/option_groups/create.blade.php
   resources/views/staff/option_groups/edit.blade.php

3) เพิ่ม routes:
   นำโค้ดจาก routes/staff_option_groups_crud.routes.snippet.php ไป merge ใน routes/web.php ภายใน staff group เดิม

4) เพิ่มเมนูใน navbar staff:
   ดู snippet: resources/views/layouts/staff.navbar.option_groups.snippet.blade.php

## เงื่อนไข
- ต้องมี tables: option_groups, option_group_items, options แล้ว (migrate แล้ว)
- Model OptionGroup ต้องมี relation options() ตาม snippet
