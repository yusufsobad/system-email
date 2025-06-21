<page class="style1" backtop="10mm" backbottom="20mm" backleft="15mm" backright="15mm" pagegroup="new">
	<table style="width:100%;border-collapse:collapse;">
		<thead>
			<tr>
				<th colspan="7" style="text-transform: uppercase;font-family: calibribold;text-align:center;font-size: 14px;padding-top:10px;">Report Blast Email - </th>
			</tr>
			<tr>
				<th colspan="7">&nbsp;</th>
			</tr>
            <tr>
                <th style="width:4%;font-family:CalibriBold;text-align:center;border:1px solid #000;">No</th>
                <th style="width:14%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Nama Subject Email</th>
                <th style="width:8%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Database Group</th>
                <th style="width:8%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Tanggal Kirim</th>
                <th style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Status</th>
                <th style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Reading</th>
                <th style="width:6%;font-family:CalibriBold;text-align:center;border:1px solid #000;">Click Link</th>
            </tr>
		</thead>
		<tbody>
			<?php
			$no = 0;
			foreach ($data as $key => $value) {
                $no += 1;
                $id_meta = $value['ID'];

                $from = $value['type_to_m']==4?'Group : '.$value['name_to_m']:$value['email_to_m'];

                // Check jumlah email
                $tMail = kmi_send::get_log_meta($id_meta);
                $pMail = kmi_send::get_log_meta($id_meta,"AND status IN ('0')");
                $sMail = kmi_send::get_log_meta($id_meta,"AND status IN ('1')");
                $fMail = kmi_send::get_log_meta($id_meta,"AND status IN ('2')");
                $rMail = kmi_send::get_log_meta($id_meta,"AND status IN ('4','5')");
                $lMail = kmi_send::get_log_meta($id_meta,"AND status='5'");

                if(count($fMail)<1 && count($sMail)<1 && count($pMail)<1){
                    $value['status'] = 3;
                }	

                $status = send_mail::_conv_status($value['status']);
                $read = count($rMail) .'/'. count($tMail);
                $link = count($lMail) .'/'. count($tMail);

                echo '
                <tr>
                    <td style="width:4%;font-family:Calibri;text-align:center;border:1px solid #000;">' . $no . '</td>
                    <td style="width:23%;font-family:Calibri;border:1px solid #000;">' . $value['subject_mail'] . '</td>
                    <td style="width:23%;font-family:Calibri;border:1px solid #000;">' . $from . '</td>
                    <td style="width:14%;font-family:Calibri;text-align:center;border:1px solid #000;">' . format_date_id($value['date']) . '</td>
                    <td style="width:12%;font-family:Calibri;border:1px solid #000;">' . $status . '</td>
                    <td style="width:12%;font-family:Calibri;border:1px solid #000;">' . $read . '</td>
                    <td style="width:12%;font-family:Calibri;border:1px solid #000;">' . $link . '</td>
                </tr>
                ';
            }

			?>
		</tbody>
	</table>
</page>