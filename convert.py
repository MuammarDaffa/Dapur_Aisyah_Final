import os
import re

REPLACEMENTS = {
    r'\bitems-center\b': 'align-items-center',
    r'\bjustify-between\b': 'justify-content-between',
    r'\bjustify-center\b': 'justify-content-center',
    r'\bjustify-end\b': 'justify-content-end',
    r'\bjustify-start\b': 'justify-content-start',
    r'\bflex-col\b': 'flex-column',
    r'\bflex-row\b': 'flex-row',
    r'\bflex\b': 'd-flex',
    r'\bw-full\b': 'w-100',
    r'\bh-full\b': 'h-100',
    r'\bh-screen\b': 'vh-100',
    r'\bmin-h-screen\b': 'min-vh-100',
    r'\bgrid\b': 'row',
    r'\bgrid-cols-\d+\b': 'col',
    r'\bmd:grid-cols-\d+\b': 'col-md',
    r'\bgap-\d+\b': 'g-3',
    r'\bmt-(\d+)\b': r'mt-\1',
    r'\bmb-(\d+)\b': r'mb-\1',
    r'\bml-(\d+)\b': r'ms-\1',
    r'\bmr-(\d+)\b': r'me-\1',
    r'\bmx-(\d+)\b': r'mx-\1',
    r'\bmy-(\d+)\b': r'my-\1',
    r'\bpt-(\d+)\b': r'pt-\1',
    r'\bpb-(\d+)\b': r'pb-\1',
    r'\bpl-(\d+)\b': r'ps-\1',
    r'\bpr-(\d+)\b': r'pe-\1',
    r'\bpx-(\d+)\b': r'px-\1',
    r'\bpy-(\d+)\b': r'py-\1',
    r'\bp-(\d+)\b': r'p-\1',
    
    r'\btext-xs\b': 'small',
    r'\btext-sm\b': 'fs-6',
    r'\btext-lg\b': 'fs-5',
    r'\btext-xl\b': 'fs-4',
    r'\btext-2xl\b': 'fs-3',
    r'\btext-3xl\b': 'fs-2',
    r'\btext-4xl\b': 'fs-1',
    r'\bfont-bold\b': 'fw-bold',
    r'\bfont-semibold\b': 'fw-bold',
    r'\bfont-medium\b': 'fw-medium',
    r'\btext-center\b': 'text-center',
    r'\btext-right\b': 'text-end',
    r'\btext-left\b': 'text-start',
    
    r'\btext-white\b': 'text-white',
    r'\btext-black\b': 'text-dark',
    r'\btext-gray-\d+\b': 'text-secondary',
    r'\btext-red-\d+\b': 'text-danger',
    r'\btext-orange-\d+\b': 'text-primary',
    r'\btext-green-\d+\b': 'text-success',
    r'\btext-blue-\d+\b': 'text-info',
    r'\btext-amber-\d+\b': 'text-warning',
    r'\btext-yellow-\d+\b': 'text-warning',
    
    r'\bbg-white\b': 'bg-white',
    r'\bbg-black\b': 'bg-dark',
    r'\bbg-gray-\d+\b': 'bg-light',
    r'\bbg-red-\d+\b': 'bg-danger text-white',
    r'\bbg-orange-\d+\b': 'bg-primary text-white',
    r'\bbg-amber-\d+\b': 'bg-warning text-dark',
    r'\bbg-yellow-\d+\b': 'bg-warning text-dark',
    r'\bbg-green-\d+\b': 'bg-success text-white',
    r'\bbg-blue-\d+\b': 'bg-info text-white',
    r'\bbg-indigo-\d+\b': 'bg-secondary text-white',
    r'\bbg-gradient-to-[a-z]+\b': '',
    r'\bfrom-[a-z]+-\d+\b': '',
    r'\bto-[a-z]+-\d+\b': '',

    r'\brounded-xl\b': 'rounded',
    r'\brounded-lg\b': 'rounded',
    r'\brounded-md\b': 'rounded',
    r'\brounded-full\b': 'rounded-pill',
    r'\brounded\b': 'rounded',
    
    r'\bshadow-sm\b': 'shadow-sm',
    r'\bshadow-lg\b': 'shadow',
    r'\bshadow-xl\b': 'shadow-lg',
    r'\bshadow\b': 'shadow',
    
    r'\bborder-gray-\d+\b': 'border border-secondary',
    r'\bborder-orange-\d+\b': 'border border-primary',
    r'\bborder\b': 'border',

    r'\bhover:bg-[a-z]+-\d+\b': '',
    r'\bhover:text-[a-z]+-\d+\b': '',
    r'\bhover:shadow-lg\b': '',
    r'\bhover:shadow-\w+-\d+\b': '',
    r'\btransition-all\b': '',
    r'\btransition-colors\b': '',
    r'\bduration-\d+\b': '',
    r'\btransform\b': '',
    r'\bhover:scale-\d+\b': '',
    r'\bscale-\d+\b': '',
    
    r'\bhidden\b': 'd-none',
    r'\bmd:flex\b': 'd-md-flex',
    r'\bmd:hidden\b': 'd-md-none',
    r'\bsm:flex\b': 'd-sm-flex',
    r'\bsm:hidden\b': 'd-sm-none',
    r'\blg:flex\b': 'd-lg-flex',
    r'\blg:hidden\b': 'd-lg-none',
    r'\bblock\b': 'd-block',
    r'\binline-block\b': 'd-inline-block',
    r'\binline\b': 'd-inline',
    r'\bmd:block\b': 'd-md-block',
    r'\bsm:block\b': 'd-sm-block',
    
    r'\babsolute\b': 'position-absolute',
    r'\brelative\b': 'position-relative',
    r'\bfixed\b': 'position-fixed',
    r'\bsticky\b': 'sticky-top',
    
    r'\btop-\d+\b': '',
    r'\bbottom-\d+\b': '',
    r'\bleft-\d+\b': '',
    r'\bright-\d+\b': '',
    r'\binset-\d+\b': '',
    r'\bz-\d+\b': '',
    
    # Forms & Inputs
    r'\bfocus:ring-\w+-\d+\b': '',
    r'\bfocus:border-\w+-\d+\b': '',
    r'\bfocus:ring\b': '',
    r'\boutline-none\b': '',
    r'\bappearance-none\b': '',
    r'\bbg-transparent\b': 'bg-transparent',
    
    # Specific component mappings (rough)
    r'\bmax-w-7xl\b': 'container',
    r'\bmx-auto\b': 'mx-auto',
    
    # AlpineJS transitions (remove them to look rigid)
    r'\bx-transition:enter[\w-]*="[^"]*"': '',
    r'\bx-transition:leave[\w-]*="[^"]*"': '',
    r'\bx-transition\b': '',
}

def replace_classes(match):
    class_str = match.group(1)
    for pattern, repl in REPLACEMENTS.items():
        class_str = re.sub(pattern, repl, class_str)
    
    # Remove duplicate spaces
    class_str = re.sub(r'\s+', ' ', class_str).strip()
    return f'class="{class_str}"'

def replace_classes_single(match):
    class_str = match.group(1)
    for pattern, repl in REPLACEMENTS.items():
        class_str = re.sub(pattern, repl, class_str)
    
    # Remove duplicate spaces
    class_str = re.sub(r'\s+', ' ', class_str).strip()
    return f"class='{class_str}'"

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = re.sub(r'class="([^"]*)"', replace_classes, content)
    new_content = re.sub(r"class='([^']*)'", replace_classes_single, new_content)

    # Some hardcoded ugly fixes for layout wrappers
    # Add 'form-control' to text inputs where missing
    new_content = re.sub(r'<input\s+([^>]*?)type="text"([^>]*?)class="([^"]*)"', r'<input \1type="text"\2class="form-control \3"', new_content)
    new_content = re.sub(r'<input\s+([^>]*?)type="email"([^>]*?)class="([^"]*)"', r'<input \1type="email"\2class="form-control \3"', new_content)
    new_content = re.sub(r'<input\s+([^>]*?)type="password"([^>]*?)class="([^"]*)"', r'<input \1type="password"\2class="form-control \3"', new_content)
    new_content = re.sub(r'<input\s+([^>]*?)type="number"([^>]*?)class="([^"]*)"', r'<input \1type="number"\2class="form-control \3"', new_content)
    new_content = re.sub(r'<textarea\s+([^>]*?)class="([^"]*)"', r'<textarea \1class="form-control \2"', new_content)
    new_content = re.sub(r'<select\s+([^>]*?)class="([^"]*)"', r'<select \1class="form-select \2"', new_content)
    
    # Turn buttons into bootstrap buttons
    new_content = re.sub(r'<button\s+([^>]*?)class="([^"]*?bg-primary[^"]*?)"', r'<button \1class="btn btn-primary \2"', new_content)
    new_content = re.sub(r'<button\s+([^>]*?)class="([^"]*?bg-danger[^"]*?)"', r'<button \1class="btn btn-danger \2"', new_content)
    new_content = re.sub(r'<button\s+([^>]*?)class="([^"]*?text-danger[^"]*?)"', r'<button \1class="btn btn-outline-danger \2"', new_content)

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
