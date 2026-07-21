<?php
$file = __DIR__ . '/resources/views/pelanggan/products.blade.php';
$content = file_get_contents($file);

$jsNew = <<<EOT
        // Use extras from currentProduct
        availableExtras = currentProduct.extras || [];
        document.getElementById('modalExtrasLoading').classList.add('hidden');
        
        if (availableExtras.length > 0) {
            let html = '';
            availableExtras.forEach(extra => {
                html += `
                    <div class="d-flex align-items-center justify-content-between py-2.5 px-2 border-b border border-secondary last:border-b-0 hover:bg-primary text-white/50 rounded g-3">
                        <label class="d-flex align-items-center g-3.5 cursor-pointer d-flex-1 min-w-0">
                            <input type="checkbox" name="extras[\${extra.id}][id]" value="\${extra.id}" data-harga="\${extra.harga}" id="extra_cb_\${extra.id}" onchange="toggleExtra(\${extra.id})" style="width: 16px; height: 16px;" class="rounded border border-secondary text-primary extra-checkbox flex-shrink-0">
                            <span class="fs-6 fw-medium text-secondary truncate">\${extra.nama_extra}</span>
                        </label>
                        <div class="d-flex align-items-center g-3.5 flex-shrink-0">
                            <span class="fs-6 fw-bold text-primary flex-shrink-0">+\${formatRupiah(extra.harga)}</span>
                            <div id="extra_qty_container_\${extra.id}" class="d-none align-items-center g-3">
                                <button type="button" onclick="changeExtraQty(\${extra.id}, -1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">−</button>
                                <input type="text" inputmode="none" readonly tabindex="-1" name="extras[\${extra.id}][qty]" id="extra_qty_\${extra.id}" value="0" class="form-control w-12 text-center py-1 rounded border border border-secondary small fw-bold text-secondary focus: cursor-default select-none bg-light flex-shrink-0" disabled>
                                <button type="button" onclick="changeExtraQty(\${extra.id}, 1)" class="w-7 h-7 d-flex align-items-center justify-content-center bg-light hover:bg-light rounded fw-bold text-secondary fs-6 flex-shrink-0">+</button>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('modalExtrasList').innerHTML = html;
            document.getElementById('modalExtrasContainer').classList.remove('hidden');
        }

        updateModalTotal();
        document.getElementById('orderModal').style.display = 'flex';
EOT;

$content = preg_replace('/fetch\(\`\/api\/produk\/\$\{currentProduct\.id\}\/extras\`\).*?\.catch\(err => \{.*?\}\);/s', $jsNew, $content);
$content = str_replace('document.getElementById(\'orderModal\').style.display = \'flex\';', '', $content); // remove the extra one at bottom

file_put_contents($file, $content);
echo "JS updated.\n";
