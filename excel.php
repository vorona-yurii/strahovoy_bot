<?php
/**
 * Created by PhpStorm.
 * User: yuv
 * Date: 24.03.2018
 * Time: 15:15
 */
require 'vendor/autoload.php';
require_once "config.php";

/**
 * @param string $sheetname
 * @param int $days_count
 * @param bool $civil
 * @return mixed
 * @throws \PhpOffice\PhpSpreadsheet\Exception
 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
 */
function getCofFromTableExcel($sheetname = '30000', $days_count = 3, $civil = true){

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setLoadSheetsOnly($sheetname);

    $spreadsheet = $reader->load(EXCEL_TABLE);

    $coordinate = 'A1';

    if($sheetname == '30000'){

        if($civil){
            switch ($days_count){
                case 3: $coordinate = 'D16'; break;
                case 4: $coordinate = 'E16'; break;
                case 5: $coordinate = 'F16'; break;
                case 6: $coordinate = 'G16'; break;
                case 7: $coordinate = 'H16'; break;
                case 8: $coordinate = 'I16'; break;
                case 9: $coordinate = 'J16'; break;
                case 10: $coordinate = 'K16'; break;
                case 11: $coordinate = 'L16'; break;
                case 12: $coordinate = 'M16'; break;
                case 13: $coordinate = 'N16'; break;
                case 14: $coordinate = 'O16'; break;
                case 15: $coordinate = 'P16'; break;
                case 16: $coordinate = 'Q16'; break;
                case 17: $coordinate = 'R16'; break;
                case 18: $coordinate = 'S16'; break;
                case 19: $coordinate = 'D25'; break;
                case 20: $coordinate = 'E25'; break;
                case 21: $coordinate = 'F25'; break;
                case 22: $coordinate = 'G25'; break;
                case 23: $coordinate = 'H25'; break;
                case 24: $coordinate = 'I25'; break;
                case 25: $coordinate = 'J25'; break;
                case 26: $coordinate = 'K25'; break;
                case 27: $coordinate = 'L25'; break;
                case 28: $coordinate = 'M25'; break;
                case 29: $coordinate = 'N25'; break;
                case 30: $coordinate = 'O25'; break;
                case (($days_count > 30 && $days_count <= 60) ? true : false): $coordinate = 'P25'; break;
                case (($days_count > 60 && $days_count <= 90) ? true : false): $coordinate = 'Q25'; break;
                case (($days_count > 90 && $days_count <= 180) ? true : false): $coordinate = 'R25'; break;
                case (($days_count > 180 && $days_count <= 365) ? true : false): $coordinate = 'S25'; break;
            }
        }else{
            switch ($days_count){
                case 3: $coordinate = 'D14'; break;
                case 4: $coordinate = 'E14'; break;
                case 5: $coordinate = 'F14'; break;
                case 6: $coordinate = 'G14'; break;
                case 7: $coordinate = 'H14'; break;
                case 8: $coordinate = 'I14'; break;
                case 9: $coordinate = 'J14'; break;
                case 10: $coordinate = 'K14'; break;
                case 11: $coordinate = 'L14'; break;
                case 12: $coordinate = 'M14'; break;
                case 13: $coordinate = 'N14'; break;
                case 14: $coordinate = 'O14'; break;
                case 15: $coordinate = 'P14'; break;
                case 16: $coordinate = 'Q14'; break;
                case 17: $coordinate = 'R14'; break;
                case 18: $coordinate = 'S14'; break;
                case 19: $coordinate = 'D23'; break;
                case 20: $coordinate = 'E23'; break;
                case 21: $coordinate = 'F23'; break;
                case 22: $coordinate = 'G23'; break;
                case 23: $coordinate = 'H23'; break;
                case 24: $coordinate = 'I23'; break;
                case 25: $coordinate = 'J23'; break;
                case 26: $coordinate = 'K23'; break;
                case 27: $coordinate = 'L23'; break;
                case 28: $coordinate = 'M23'; break;
                case 29: $coordinate = 'N23'; break;
                case 30: $coordinate = 'O23'; break;
                case (($days_count > 30 && $days_count <= 60) ? true : false): $coordinate = 'P23'; break;
                case (($days_count > 60 && $days_count <= 90) ? true : false): $coordinate = 'Q23'; break;
                case (($days_count > 90 && $days_count <= 180) ? true : false): $coordinate = 'R23'; break;
                case (($days_count > 180 && $days_count <= 365) ? true : false): $coordinate = 'S23'; break;
            }

        }

    }elseif($sheetname == '50000'){

        if($civil){
            switch ($days_count){
                case 3: $coordinate = 'D14'; break;
                case 4: $coordinate = 'E14'; break;
                case 5: $coordinate = 'F14'; break;
                case 6: $coordinate = 'G14'; break;
                case 7: $coordinate = 'H14'; break;
                case 8: $coordinate = 'I14'; break;
                case 9: $coordinate = 'J14'; break;
                case 10: $coordinate = 'K14'; break;
                case 11: $coordinate = 'L14'; break;
                case 12: $coordinate = 'M14'; break;
                case 13: $coordinate = 'N14'; break;
                case 14: $coordinate = 'O14'; break;
                case 15: $coordinate = 'P14'; break;
                case 16: $coordinate = 'Q14'; break;
                case 17: $coordinate = 'R14'; break;
                case 18: $coordinate = 'S14'; break;
                case 19: $coordinate = 'D22'; break;
                case 20: $coordinate = 'E22'; break;
                case 21: $coordinate = 'F22'; break;
                case 22: $coordinate = 'G22'; break;
                case 23: $coordinate = 'H22'; break;
                case 24: $coordinate = 'I22'; break;
                case 25: $coordinate = 'J22'; break;
                case 26: $coordinate = 'K22'; break;
                case 27: $coordinate = 'L22'; break;
                case 28: $coordinate = 'M22'; break;
                case 29: $coordinate = 'N22'; break;
                case 30: $coordinate = 'O22'; break;
                case (($days_count > 30 && $days_count <= 60) ? true : false): $coordinate = 'P22'; break;
                case (($days_count > 60 && $days_count <= 90) ? true : false): $coordinate = 'Q22'; break;
                case (($days_count > 90 && $days_count <= 180) ? true : false): $coordinate = 'R22'; break;
                case (($days_count > 180 && $days_count <= 365) ? true : false): $coordinate = 'S22'; break;
            }
        }else{
            switch ($days_count){
                case 3: $coordinate = 'D12'; break;
                case 4: $coordinate = 'E12'; break;
                case 5: $coordinate = 'F12'; break;
                case 6: $coordinate = 'G12'; break;
                case 7: $coordinate = 'H12'; break;
                case 8: $coordinate = 'I12'; break;
                case 9: $coordinate = 'J12'; break;
                case 10: $coordinate = 'K12'; break;
                case 11: $coordinate = 'L12'; break;
                case 12: $coordinate = 'M12'; break;
                case 13: $coordinate = 'N12'; break;
                case 14: $coordinate = 'O12'; break;
                case 15: $coordinate = 'P12'; break;
                case 16: $coordinate = 'Q12'; break;
                case 17: $coordinate = 'R12'; break;
                case 18: $coordinate = 'S12'; break;
                case 19: $coordinate = 'D20'; break;
                case 20: $coordinate = 'E20'; break;
                case 21: $coordinate = 'F20'; break;
                case 22: $coordinate = 'G20'; break;
                case 23: $coordinate = 'H20'; break;
                case 24: $coordinate = 'I20'; break;
                case 25: $coordinate = 'J20'; break;
                case 26: $coordinate = 'K20'; break;
                case 27: $coordinate = 'L20'; break;
                case 28: $coordinate = 'M20'; break;
                case 29: $coordinate = 'N20'; break;
                case 30: $coordinate = 'O20'; break;
                case (($days_count > 30 && $days_count <= 60) ? true : false): $coordinate = 'P20'; break;
                case (($days_count > 60 && $days_count <= 90) ? true : false): $coordinate = 'Q20'; break;
                case (($days_count > 90 && $days_count <= 180) ? true : false): $coordinate = 'R20'; break;
                case (($days_count > 180 && $days_count <= 365) ? true : false): $coordinate = 'S20'; break;
            }

        }

    }

    $elem = $spreadsheet->getActiveSheet()->getCell($coordinate)->getValue();

    return $elem;
}
