import json
import sys
import re
import os

def inject_comments(data_file):
    with open(data_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
        
    for item in data:
        filepath = item['filepath']
        comments = item['comments'] # dict of line_number (string): comment_string
        
        if not os.path.exists(filepath):
            print(f"File not found: {filepath}")
            continue
            
        with open(filepath, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            
        # Insert in reverse order so line numbers don't shift for subsequent inserts
        for line_num in sorted([int(k) for k in comments.keys()], reverse=True):
            idx = line_num - 1
            if 0 <= idx < len(lines):
                target_line = lines[idx]
                indent = re.match(r'^\s*', target_line).group(0)
                
                comment_lines = comments[str(line_num)].strip().split('\n')
                formatted_comment = ''
                for cl in comment_lines:
                    formatted_comment += f"{indent}{cl}\n"
                    
                lines.insert(idx, formatted_comment)
            else:
                print(f"Line number {line_num} out of range for {filepath}")
                
        with open(filepath, 'w', encoding='utf-8') as f:
            f.writelines(lines)
            
        print(f"Documented {filepath}")

if __name__ == '__main__':
    inject_comments(sys.argv[1])
