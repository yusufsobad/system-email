<page class="style1" backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
	<table style="width:100%;border-collapse:collapse;">
		<thead>
			<tr>
				<th colspan="16" style="text-transform: uppercase;font-family: calibribold;text-align:center;font-size: 14px;padding-top:10px;">Detail Data</th>
			</tr>
			<tr>
				<th colspan="16">&nbsp;</th>
			</tr>
            <tr>
                <th rowspan="2" style="width:4%;font-family:CalibriBold;text-align:center;border:1px solid #000;">No</th>
                <th rowspan="2" style="width:14%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Nama Subject Email</th>
                <th rowspan="2" style="width:8%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Database Group</th>
                <th rowspan="2" style="width:8%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Tanggal Kirim</th>
                <th rowspan="2" style="width:10%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Nama Penerima</th>
                <th rowspan="2" style="width:10%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Nama Instansi</th>
                <th rowspan="2" style="width:10%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Email</th>
                <th colspan="2" style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Sukses</th>
                <th colspan="2" style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Reading</th>
                <th colspan="2" style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Click Link</th>
            </tr>
            <tr>
                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Tanggal</th>
                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Jam</th>

                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Tanggal</th>
                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Jam</th>

                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Tanggal</th>
                <th style="width:5%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Jam</th>
            </tr>
		</thead>
		<tbody>
			<?php
			$no = 0;
			foreach ($details as $key => $value) {
                $no += 1;

                $from = $data['type_to_m']==4?'Group : '.$data['name_to_m']:$data['email_to_m'];

                // ────── ambil raw value ──────
                $metaRaw  = $value['meta_date']  ?? null;      // kolom 1 (meta)
                $readRaw = $value['read_date']  ?? null;      // kolom 2 (read #1)
                $linkRaw = $value['link_date']  ?? null;      // kolom 3 (read #2) – ubah jika punya kunci berbeda

                // ────── fungsi format singkat ──────
                $fmtDate = function ($raw) {
                    return (!empty($raw) && $raw !== '0000-00-00 00:00:00')
                        ? format_date_id($raw)
                        : '-';
                };
                $fmtTime = function ($raw) {
                    return (!empty($raw) && $raw !== '0000-00-00 00:00:00')
                        ? date('H:i:s',   strtotime($raw))
                        : '-';
                };

                // ────── hasil format ──────
                $metaDate  = $fmtDate($metaRaw);
                $metaTime  = $fmtTime($metaRaw);

                $readDate = $fmtDate($readRaw);
                $readTime = $fmtTime($readRaw);

                $linkDate = $fmtDate($linkRaw);
                $linkTime = $fmtTime($linkRaw);

                // ────── buat satu baris HTML ──────

                echo '
                <tr>
                    <td style="width:4%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $no . '</td>
                    <td style="width:14%;font-family:Calibri;border:1px solid #000;">' . $data['subject_mail'] . '</td>
                    <td style="width:8%;font-family:Calibri;border:1px solid #000;">' . $from . '</td>
                    <td style="width:8%;font-family:Calibri;text-align:center;border:1px solid #000;">' . format_date_id($data['date']) . '</td>
                    <td style="width:10%;font-family:Calibri;border:1px solid #000;">' . $value['name_meta'] . '</td>
                    <td style="width:10%;font-family:Calibri;border:1px solid #000;">' . $value['place_meta'] . '</td>
                    <td style="width:10%;font-family:Calibri;border:1px solid #000;">' . $value['email_meta'] . '</td>
                    
                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $metaDate . '</td>
                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $metaTime . '</td>

                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $readDate . '</td>
                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $readTime . '</td>

                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $linkDate . '</td>
                    <td style="width:5%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $linkTime . '</td>
                </tr>
                ';
            }

			?>
		</tbody>
	</table>
</page>