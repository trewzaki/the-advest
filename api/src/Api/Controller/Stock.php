<?php
namespace Api\Controller;

use Security\MrMeSecurity as MrMeSecurity;
use MrMe\Web\Validate as WebValidate;
use MrMe\Web\Controller;

use MrMe\Database\MySql\MySqlCommand;
use MrMe\Database\MySql\MySqlConnection;


class Stock extends Controller
{
    public function get()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: POST");  
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');

		$stock_list = $this->request->body->stock_list;
		$return 	= $this->request->body->return;
		$year 		= $this->request->body->year;

		// $stock_list_explode = explode(",", $stock_list);

		// $return_percent = $return / 100;

		// $return_percent = round($return_percent, 2);
		// var_dump($return_percent);

		// $return_percent = sprintf("%0.2f", $return / 100);

		// if ($return_percent == "0.14") {
		// 	echo "eiei";
		// }

		// var_dump($return_percent);
		
		$this->db->select("`stock`", $stock_list);
		
		$model = $this->db->executeReader();

		$isNull = false;

		$count = 0;
		foreach ($model as $m) 
		{
			foreach ($m as $m_mini => $value) 
			{
				if (is_null($value))
				{
					$isNull = true;
					break;
				}
			}
			if (!$isNull) 
				$count++;
			
			$isNull = false;
		}

		$model1 = array("w0" => 0,
						"w1" => 0,
						"w2" => 0,
						"w3" => 0,
						"w4" => 0);

		$model2 = array("w0" => 0,
						"w1" => 0,
						"w2" => 0,
						"w3" => 0,
						"w4" => 0);

		$model3 = array("w0" => 0,
						"w1" => 0,
						"w2" => 0,
						"w3" => 0,
						"w4" => 0);

		for ($year=5; $year>0; $year-=2)
		{
			switch ($year) 
			{
				case 3:
					if ($count > 732)
						$count = 732;
					break;
				
				case 1:
					if ($count > 245)
					$count = 245;
					break;
			}

			$cov00 = 0.0;
			$cov01 = 0.0;
			$cov02 = 0.0;
			$cov03 = 0.0;
			$cov04 = 0.0;
			$cov11 = 0.0;
			$cov12 = 0.0;
			$cov13 = 0.0;
			$cov14 = 0.0;
			$cov22 = 0.0;
			$cov23 = 0.0;
			$cov24 = 0.0;
			$cov33 = 0.0;
			$cov34 = 0.0;
			$cov44 = 0.0;

			$avg0 = 0.0;
			$avg1 = 0.0;
			$avg2 = 0.0;
			$avg3 = 0.0;
			$avg4 = 0.0;

			$sum_square0 = 0.0;
			$sum_square1 = 0.0;
			$sum_square2 = 0.0;
			$sum_square3 = 0.0;
			$sum_square4 = 0.0;

			$sd0 = 0.0;
			$sd1 = 0.0;
			$sd2 = 0.0;
			$sd3 = 0.0;
			$sd4 = 0.0;

			$w0 = 0.0;
			$w1 = 0.0;
			$w2 = 0.0;
			$w3 = 0.0;
			$w4 = 0.0;

			$sd_square_p = 100000.0;

			$loop_number = 0;
			foreach ($model as $m) 
			{
				if ($loop_number++ == $count)
					break;

				$tmp_array = (array) ($m);
				$tmp_array = array_values($tmp_array);

				$cov00 += $tmp_array[0] * $tmp_array[0];
				$cov01 += $tmp_array[0] * $tmp_array[1];
				$cov02 += $tmp_array[0] * $tmp_array[2];
				$cov03 += $tmp_array[0] * $tmp_array[3];
				$cov04 += $tmp_array[0] * $tmp_array[4];
				$cov11 += $tmp_array[1] * $tmp_array[1];
				$cov12 += $tmp_array[1] * $tmp_array[2];
				$cov13 += $tmp_array[1] * $tmp_array[3];
				$cov14 += $tmp_array[1] * $tmp_array[4];
				$cov22 += $tmp_array[2] * $tmp_array[2];
				$cov23 += $tmp_array[2] * $tmp_array[3];
				$cov24 += $tmp_array[2] * $tmp_array[4];
				$cov33 += $tmp_array[3] * $tmp_array[3];
				$cov34 += $tmp_array[3] * $tmp_array[4];
				$cov44 += $tmp_array[4] * $tmp_array[4];

				$avg0 += $tmp_array[0];
				$avg1 += $tmp_array[1];
				$avg2 += $tmp_array[2];
				$avg3 += $tmp_array[3];
				$avg4 += $tmp_array[4];

				$sum_square0 += pow($tmp_array[0], 2);
				$sum_square1 += pow($tmp_array[1], 2);
				$sum_square2 += pow($tmp_array[2], 2);
				$sum_square3 += pow($tmp_array[3], 2);
				$sum_square4 += pow($tmp_array[4], 2);

				// var_dump($tmp_array);
				// break;
			}

			$sd0 = sqrt((($count*$sum_square0) - (pow($avg0, 2)))/($count*($count-1))) / 100;
			$sd1 = sqrt((($count*$sum_square1) - (pow($avg1, 2)))/($count*($count-1))) / 100;
			$sd2 = sqrt((($count*$sum_square2) - (pow($avg2, 2)))/($count*($count-1))) / 100;
			$sd3 = sqrt((($count*$sum_square3) - (pow($avg3, 2)))/($count*($count-1))) / 100;
			$sd4 = sqrt((($count*$sum_square4) - (pow($avg4, 2)))/($count*($count-1))) / 100;

			$avg0 = pow((1 + (($avg0/$count)/100)), 245) -1;
			$avg1 = pow((1 + (($avg1/$count)/100)), 245) -1;
			$avg2 = pow((1 + (($avg2/$count)/100)), 245) -1;
			$avg3 = pow((1 + (($avg3/$count)/100)), 245) -1;
			$avg4 = pow((1 + (($avg4/$count)/100)), 245) -1;

			$cov00 = ($cov00/($count-1))/10000;
			$cov01 = ($cov01/($count-1))/10000;
			$cov02 = ($cov02/($count-1))/10000;
			$cov03 = ($cov03/($count-1))/10000;
			$cov04 = ($cov04/($count-1))/10000;
			$cov11 = ($cov11/($count-1))/10000;
			$cov12 = ($cov12/($count-1))/10000;
			$cov13 = ($cov13/($count-1))/10000;
			$cov14 = ($cov14/($count-1))/10000;
			$cov22 = ($cov22/($count-1))/10000;
			$cov23 = ($cov23/($count-1))/10000;
			$cov24 = ($cov24/($count-1))/10000;
			$cov33 = ($cov33/($count-1))/10000;
			$cov34 = ($cov34/($count-1))/10000;
			$cov44 = ($cov44/($count-1))/10000;

			$r01 = $cov01/($sd0*$sd1);
			$r02 = $cov02/($sd0*$sd2);
			$r03 = $cov03/($sd0*$sd3);
			$r04 = $cov04/($sd0*$sd4);
			$r12 = $cov12/($sd1*$sd2);
			$r13 = $cov13/($sd1*$sd3);
			$r14 = $cov14/($sd1*$sd4);
			$r23 = $cov23/($sd2*$sd3);
			$r24 = $cov24/($sd2*$sd4);
			$r34 = $cov34/($sd3*$sd4);

			$sd01 = $sd0 * $sd1;
			$sd02 = $sd0 * $sd2;
			$sd03 = $sd0 * $sd3;
			$sd04 = $sd0 * $sd4;
			$sd12 = $sd1 * $sd2;
			$sd13 = $sd1 * $sd3;
			$sd14 = $sd1 * $sd4;
			$sd23 = $sd2 * $sd3;
			$sd24 = $sd2 * $sd4;
			$sd34 = $sd3 * $sd4;

			$sd0_square = pow($sd0, 2);
			$sd1_square = pow($sd1, 2);
			$sd2_square = pow($sd2, 2);
			$sd3_square = pow($sd3, 2);
			$sd4_square = pow($sd4, 2);

			$isContinue = false;

			for ($i0=0; $i0<=100; $i0++) 
			{
				for ($i1=0; $i1<=100; $i1++)
				{
					if ($i0 + $i1 > 100)
						break;

					for ($i2=0; $i2<=100; $i2++)
					{
						if ($i0 + $i1 + $i2 > 100)
							break;

						for ($i3=0; $i3<=100; $i3++)
						{
							if ($i0 + $i1 + $i2 + $i3 > 100)
								break;

							$i4 = 100 - $i0 - $i1 - $i2 - $i3;

							$ep = ($i0*$avg0) + ($i1*$avg1) + ($i2*$avg2) + ($i3*$avg3) + ($i4*$avg4);
							$ep = round($ep);

							if ($return == $ep)
							{
								$tmp_sd_square_p = 	(pow($i0, 2)*$sd0_square) + (pow($i1, 2)*$sd1_square) + (pow($i2, 2)*$sd2_square) + (pow($i3, 2)*$sd3_square) + (pow($i4, 2)*$sd4_square) + 
												2 * (  $i0 * ( ($i1*$sd01*$r01) + ($i2*$sd02*$r02) + ($i3*$sd03*$r03) + ($i4*$sd04*$r04) ) + 
													$i1 * ( ($i2*$sd12*$r12) + ($i3*$sd13*$r13) + ($i4*$sd14*$r14) ) + 
													$i2 * ( ($i3*$sd23*$r23) + ($i4*$sd24*$r24) ) +
													($i3 *   ($i4*$sd34*$r34)  ));

								if ($tmp_sd_square_p < $sd_square_p)
								{
										$sd_square_p = $tmp_sd_square_p;
										$w0 = $i0;
										$w1 = $i1;
										$w2 = $i2;
										$w3 = $i3;
										$w4 = $i4;
								}
							}
						}
					}
				}
				// $isContinue = false;
			}
			switch ($year) 
			{
				case 1:
					$model1["w0"] = $w0;
					$model1["w1"] = $w1;
					$model1["w2"] = $w2;
					$model1["w3"] = $w3;
					$model1["w4"] = $w4;
					break;
				
				case 3:
					$model2["w0"] = $w0;
					$model2["w1"] = $w1;
					$model2["w2"] = $w2;
					$model2["w3"] = $w3;
					$model2["w4"] = $w4;
					break;

				case 5:
					$model3["w0"] = $w0;
					$model3["w1"] = $w1;
					$model3["w2"] = $w2;
					$model3["w3"] = $w3;
					$model3["w4"] = $w4;
					break;
			}
		}

		$this->response->success(array("success" => true,
									   "model"	 => array("1year"  => $model1,
														  "3years" => $model2,
														  "5years" => $model3)));

		// $this->response->success(array("success" => true,
		// 							   "count"	 => $count,
		// 							   "model"	 => array("cov00" => $cov00,
		// 												  "cov01" => $cov01,
		// 												  "cov02" => $cov02,
		// 												  "cov03" => $cov03,
		// 												  "cov04" => $cov04,
		// 												  "cov11" => $cov11,
		// 												  "cov12" => $cov12,
		// 												  "cov13" => $cov13,
		// 												  "cov14" => $cov14,
		// 												  "cov22" => $cov22,
		// 												  "cov23" => $cov23,
		// 												  "cov24" => $cov24,
		// 												  "cov33" => $cov33,
		// 												  "cov34" => $cov34,
		// 												  "cov44" => $cov44,
		// 												  "avg0"  => $avg0,
		// 												  "avg1"  => $avg1,
		// 												  "avg2"  => $avg2,
		// 												  "avg3"  => $avg3,
		// 												  "avg4"  => $avg4,
		// 												  "sd0"   => $sd0,
		// 												  "sd1"   => $sd1,
		// 												  "sd2"   => $sd2,
		// 												  "sd3"   => $sd3,
		// 												  "sd4"   => $sd4)));
	}
	
	public function getReturn()
	{
		header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: POST");  
        header('Access-Control-Allow-Methods: *');
		header('Content-Type: application/json');
		
		$stock_list = $this->request->body->stock_list;
		// $year 		= $this->request->body->year;

		// $this->db->select("`stock_avg`", $stock_list);

		// switch ($year) 
		// {
		// 	case 5:
		// 		$this->db->where("id", "=", 2);
		// 		break;
			
		// 	case 3:
		// 		$this->db->where("id", "=", 3);
		// 		break;

		// 	case 1:
		// 		$this->db->where("id", "=", 4);
		// 		break;
		// }

		// $model = $this->db->executeReader();

		// foreach ($model as $m) 
		// {
		// 	foreach ($m as $m_mini => $value) 
		// 	{
		// 		if ($value > 0)
		// 		{
		// 			if ($value > $max)
		// 				$max = $value;
		// 			if ($value < $min)
		// 				$min = $value;
		// 		}
		// 	}
		// }

		// $min = ceil($min);
		// $max = floor($max);

		// // $this->response->success($model);

		// $this->response->success(array("success" => true,
		// 							   "model"	 => array("min" => $min,
		// 							   					  "max" => $max)));


		
		$this->db->select("`stock`", $stock_list);
		
		$model = $this->db->executeReader();

		$isNull = false;

		$count = 0;
		foreach ($model as $m) 
		{
			foreach ($m as $m_mini => $value) 
			{
				if (is_null($value))
				{
					$isNull = true;
					break;
				}
			}
			if (!$isNull) 
				$count++;
			
			$isNull = false;
		}

		$model1 = array("min" => 0,
						"max" => 0);

		$model2 = array("min" => 0,
						"max" => 0);

		$model3 = array("min" => 0,
						"max" => 0);

		for ($year=5; $year>0; $year-=2)
		{
			$min = 1000000;
			$max = -1;

			switch ($year) 
			{
				case 3:
					if ($count > 732)
						$count = 732;
					break;
				
				case 1:
					if ($count > 245)
					$count = 245;
					break;
			}

			$avg0 = 0.0;
			$avg1 = 0.0;
			$avg2 = 0.0;
			$avg3 = 0.0;
			$avg4 = 0.0;

			$loop_number = 0;
			foreach ($model as $m) 
			{
				if ($loop_number++ == $count)
					break;

				$tmp_array = (array) ($m);
				$tmp_array = array_values($tmp_array);

				$avg0 += $tmp_array[0];
				$avg1 += $tmp_array[1];
				$avg2 += $tmp_array[2];
				$avg3 += $tmp_array[3];
				$avg4 += $tmp_array[4];
			}

			$avg0 = pow((1 + (($avg0/$count)/100)), 245) -1;
			$avg1 = pow((1 + (($avg1/$count)/100)), 245) -1;
			$avg2 = pow((1 + (($avg2/$count)/100)), 245) -1;
			$avg3 = pow((1 + (($avg3/$count)/100)), 245) -1;
			$avg4 = pow((1 + (($avg4/$count)/100)), 245) -1;

			$tmp_array_avg = array($avg0*100, $avg1*100, $avg2*100, $avg3*100, $avg4*100);
			// var_dump($tmp_array_avg);

			switch ($year) 
			{
				case 1:
					foreach ($tmp_array_avg as $tma) 
					{
						if ($max < $tma)
							$max = $tma;
						if ($min > $tma && $tma > 0)
							$min = $tma;
					}
					
					$min = ceil($min);
					$max = floor($max);

					$model1["min"] = $min;
					$model1["max"] = $max;
					break;
				
				case 3:
					foreach ($tmp_array_avg as $tma) 
					{
						if ($max < $tma)
							$max = $tma;
						if ($min > $tma && $tma > 0)
							$min = $tma;
					}
										
					$min = ceil($min);
					$max = floor($max);
					
					$model2["min"] = $min;
					$model2["max"] = $max;
					break;

				case 5:
					foreach ($tmp_array_avg as $tma) 
					{
						if ($max < $tma)
							$max = $tma;
						if ($min > $tma && $tma > 0)
							$min = $tma;
					}
										
					$min = ceil($min);
					$max = floor($max);
					
					$model3["min"] = $min;
					$model3["max"] = $max;
					break;
			}
		}

		$this->response->success(array("success" => true,
									   "model"	 => array("1year"  => $model1,
														  "3years" => $model2,
														  "5years" => $model3)));
		
	}
}
?>