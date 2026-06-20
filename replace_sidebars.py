import os
import glob
import re

views_dir = '/home/kali/utils/rpl/app/Views'
php_files = glob.glob(os.path.join(views_dir, '*.php'))

pattern = re.compile(r'<!-- Menu Navigation -->\s*<nav>.*?</nav>\s*<div>\s*<div class="sidebar-divider"></div>\s*<ul class="sidebar-menu-list">.*?</ul>\s*</div>', re.DOTALL)

replacement = """<!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>"""

count = 0
for file in php_files:
    if os.path.basename(file) == 'login.php': continue
    
    with open(file, 'r') as f:
        content = f.read()
    
    if pattern.search(content):
        new_content = pattern.sub(replacement, content)
        with open(file, 'w') as f:
            f.write(new_content)
        print(f"Updated sidebar in {os.path.basename(file)}")
        count += 1

print(f"Total files updated: {count}")
