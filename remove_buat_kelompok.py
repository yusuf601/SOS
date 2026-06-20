import os
import glob
import re

views_dir = '/home/kali/utils/rpl/app/Views'
php_files = glob.glob(os.path.join(views_dir, '*.php'))

pattern = re.compile(r'[ \t]*<li class="sidebar-menu-item[^>]*>\s*<a href="[^"]*action=data_kelompok">\s*<span>Buat Kelompok</span>.*?</li>\n', re.DOTALL)

for file in php_files:
    with open(file, 'r') as f:
        content = f.read()
    
    if pattern.search(content):
        new_content = pattern.sub('', content)
        with open(file, 'w') as f:
            f.write(new_content)
        print(f"Updated {os.path.basename(file)}")
