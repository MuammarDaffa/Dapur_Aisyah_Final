import os
import re

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    
    # 1. Convert form wrappers to cards
    # Pattern: <form action="..." method="..." class="... space-y-5 ...">
    # Because 'space-y-5' was replaced or is missing, let's target form wrappers that are main forms
    # We'll just look for common tailwind form wrappers: "bg-white rounded p-4 shadow-sm border"
    new_content = re.sub(
        r'class="[^"]*bg-white[^"]*rounded[^"]*p-4[^"]*shadow-sm[^"]*border[^"]*"',
        r'class="card shadow-sm mb-4"',
        new_content
    )
    new_content = re.sub(
        r'class="[^"]*bg-white[^"]*rounded[^"]*p-6[^"]*shadow-sm[^"]*border[^"]*"',
        r'class="card shadow-sm mb-4"',
        new_content
    )
    # Then we need to add card-body inside the form. That's hard via regex. 
    # Instead, we just use Bootstrap's utilities on the form: class="border rounded p-4 bg-white mb-4"
    # Actually "card shadow-sm mb-4 p-4" works fine in Bootstrap.
    new_content = new_content.replace('class="card shadow-sm mb-4"', 'class="card shadow-sm mb-4 p-4"')

    # 2. Fix form labels
    # Pattern: <label class="...fs-6 fw-medium text-secondary mb-1">
    new_content = re.sub(
        r'<label\s+class="[^"]*mb-1[^"]*"',
        r'<label class="form-label fw-bold"',
        new_content
    )
    new_content = re.sub(
        r'<label\s+class="d-block[^"]*"',
        r'<label class="form-label fw-bold"',
        new_content
    )

    # 3. Fix form spacing: replace parent div of labels to have mb-3
    # Usually: <div> <label> ... <input> </div>
    new_content = re.sub(
        r'<div>\s*<label class="form-label',
        r'<div class="mb-3">\n            <label class="form-label',
        new_content
    )

    # 4. Buttons: Fix button padding and sizing
    new_content = re.sub(
        r'class="btn btn-primary[^"]*"',
        r'class="btn btn-primary"',
        new_content
    )
    new_content = re.sub(
        r'class="btn btn-danger[^"]*"',
        r'class="btn btn-danger"',
        new_content
    )

    # 5. Fix grids inside forms (e.g. min/max portion grids)
    new_content = new_content.replace('row row-cols-1 sm:row-cols-3', 'row row-cols-1 row-cols-md-3')
    new_content = new_content.replace('sm:col-span-1', 'col-md-4')

    # 6. Remove space-y-5 and replace with gap-3 or d-flex flex-column gap-3
    new_content = new_content.replace('space-y-5', 'd-flex flex-column gap-3')
    new_content = new_content.replace('space-y-4', 'd-flex flex-column gap-3')
    new_content = new_content.replace('space-y-3', 'd-flex flex-column gap-2')
    new_content = new_content.replace('space-y-2', 'd-flex flex-column gap-2')

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {filepath}")

def main():
    views_dir = r"E:\TA\Dapur_Aisyah\resources\views"
    for root, dirs, files in os.walk(views_dir):
        for file in files:
            if file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                process_file(filepath)

if __name__ == '__main__':
    main()
