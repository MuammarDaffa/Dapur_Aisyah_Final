import os
import re

REPLACEMENTS = {
    r'\bw-4\s+h-4\b': 'style="width: 16px; height: 16px;"',
    r'\bw-5\s+h-5\b': 'style="width: 20px; height: 20px;"',
    r'\bw-6\s+h-6\b': 'style="width: 24px; height: 24px;"',
    r'\bw-8\s+h-8\b': 'style="width: 32px; height: 32px;"',
    r'\bw-12\s+h-12\b': 'style="width: 48px; height: 48px;"',
    r'\bw-16\s+h-16\b': 'style="width: 64px; height: 64px;"',
    
    # Just width or height
    r'\bh-8\b': 'style="height: 32px;"',
    r'\bh-9\b': 'style="height: 36px;"',
    r'\bh-10\b': 'style="height: 40px;"',
    r'\bh-12\b': 'style="height: 48px;"',
    r'\bh-16\b': 'style="height: 64px;"',
    r'\bh-24\b': 'style="height: 96px;"',
    r'\bh-28\b': 'style="height: 112px;"',
    r'\bh-32\b': 'style="height: 128px;"',
    r'\bh-48\b': 'style="height: 192px;"',
    r'\bh-64\b': 'style="height: 256px;"',
    
    r'\bobject-cover\b': 'object-fit-cover',
    r'\bobject-contain\b': 'object-fit-contain',
    r'\bshrink-0\b': 'flex-shrink-0',
    
    # Text colors that might be on icons
    r'\btext-purple-500\b': 'text-primary',
    r'\bbg-purple-50\b': 'bg-light',
    
    r'\bd-inline-d-block\b': 'd-inline-block',
    r'\boverflow-d-none\b': 'overflow-hidden',
}

def replace_classes(match):
    class_str = match.group(1)
    for pattern, repl in REPLACEMENTS.items():
        if 'style=' in repl:
            continue # We will handle styles separately because they don't belong in class=""
        class_str = re.sub(pattern, repl, class_str)
    class_str = re.sub(r'\s+', ' ', class_str).strip()
    return f'class="{class_str}"'

def extract_and_inject_styles(match):
    # This function looks at the class string, pulls out things like 'w-6 h-6',
    # removes them from class, and adds a style="..." attribute to the tag.
    full_tag = match.group(0)
    class_attr = match.group(2)
    
    new_class = class_attr
    styles = []
    
    for pattern, repl in REPLACEMENTS.items():
        if 'style="' in repl:
            if re.search(pattern, new_class):
                new_class = re.sub(pattern, '', new_class)
                # Extract style string
                style_str = re.search(r'style="([^"]+)"', repl).group(1)
                styles.append(style_str)
        else:
             new_class = re.sub(pattern, repl, new_class)
    
    new_class = re.sub(r'\s+', ' ', new_class).strip()
    
    # If styles were found, inject them
    if styles:
        combined_styles = ' '.join(styles)
        # If tag already has style attribute, append to it
        if 'style="' in full_tag:
             full_tag = re.sub(r'style="([^"]*)"', lambda m: f'style="{m.group(1)} {combined_styles}"', full_tag)
        else:
             full_tag = full_tag.replace('class="', f'style="{combined_styles}" class="', 1)
             
    full_tag = full_tag.replace(f'class="{class_attr}"', f'class="{new_class}"')
    return full_tag

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    # First, handle inline styles for icons and images
    # We find any HTML tag that has a class attribute
    new_content = re.sub(r'<([a-zA-Z0-9]+)[^>]*?class="([^"]*)"[^>]*?>', extract_and_inject_styles, new_content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated sizes in: {filepath}")

def main():
    views_dir = r"E:\TA\Dapur_Aisyah\resources\views"
    for root, dirs, files in os.walk(views_dir):
        for file in files:
            if file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                process_file(filepath)

if __name__ == '__main__':
    main()
