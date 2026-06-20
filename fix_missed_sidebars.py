import re

files = ['app/Views/dashboard_asisten.php', 'app/Views/monitoring_kelas.php']
pattern = re.compile(r'<nav>\s*<div class="sidebar-menu-title">Menu Utama</div>.*?</aside>', re.DOTALL)
replacement = """<!-- Menu Navigation -->
            <?php include __DIR__ . '/components/sidebar_menu.php'; ?>
    </aside>"""

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    if pattern.search(content):
        new_content = pattern.sub(replacement, content)
        with open(f, 'w') as file:
            file.write(new_content)
        print(f"Fixed {f}")
    else:
        print(f"Pattern not found in {f}")
