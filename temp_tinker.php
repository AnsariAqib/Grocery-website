<?php
App\Models\Setting::updateOrCreate(['key' => 'font_heading'], ['group' => 'typography', 'value' => 'outfit', 'type' => 'select', 'label' => 'Heading font', 'help' => 'Used for section titles, page headings, navbar, card names and buttons across the whole site.', 'sort_order' => 100]);
App\Models\Setting::updateOrCreate(['key' => 'font_body'], ['group' => 'typography', 'value' => 'plus-jakarta-sans', 'type' => 'select', 'label' => 'Body font', 'help' => 'Used for paragraphs, descriptions, form fields and footer text.', 'sort_order' => 101]);
App\Models\Setting::forgetCache();
dump(App\Models\Setting::where('group', 'typography')->pluck('value', 'key'));
