<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<tr class="search cursor-p data" onclick="mm.viewDetail(this, event)" data-href="action"  keterangan="<?php echo $v_data['keterangan']; ?>" no_mmpem="<?php echo $v_data['no_mmpem']; ?>" data-edit="">
			<td><?php echo strtoupper($v_data['no_mmpem']); ?></td>
			<td><?php echo !empty($v_data['periode']) ? strtoupper($v_data['periode']) : '-'; ?></td>
			<td><?php echo !empty($v_data['keterangan']) ? strtoupper($v_data['keterangan']) : '-'; ?></td>
			<td class="text-right"><?php echo !empty($v_data['total_debet']) ? angkaDecimal($v_data['total_debet']) : '-'; ?></td>
            <td class="text-right"><?php echo !empty($v_data['total_kredit']) ? angkaDecimal($v_data['total_kredit']) : '-'; ?></td>
			<td class="text-right"><?php echo angkaDecimal($v_data['nilai']); ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="6">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>