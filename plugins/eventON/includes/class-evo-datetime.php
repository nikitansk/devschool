<?php
/**
 * Eventon date time class.
 *
 * @class 		EVO_generator
 * @version		2.3.13
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class evo_datetime{		
	/**	Construction function	 */
		public function __construct(){}

	/*
		input: event post meta, repeat interval, start or end time(var)
		ouput: interval corrected time
	*/
	public function get_int_correct_event_time($post_meta, $repeat_interval, $time='start'){
		if(!empty($post_meta['repeat_intervals']) && $repeat_interval>0){
			$intervals = unserialize($post_meta['repeat_intervals'][0]);
			return ($time=='start')? $intervals[$repeat_interval][0]:$intervals[$repeat_interval][1];
		}else{
			return ($time=='start')? $post_meta['evcal_srow'][0]:$post_meta['evcal_erow'][0];
		}
	}

	/*
	 * Return: array(start, end)
	 * Returns WP proper formatted corrected event time based on repeat interval provided
	 */
	public function get_correct_formatted_event_repeat_time($post_meta, $repeat_interval='', $format=''){
		$format = (!empty($format)? $format: get_option('date_format'));
		$wp_time_format = get_option('time_format');

		if(!empty($repeat_interval) && !empty($post_meta['repeat_intervals']) && $repeat_interval!='0'){
			$intervals = unserialize($post_meta['repeat_intervals'][0]);

			$formatted_unix_s = eventon_get_formatted_time($intervals[$repeat_interval][0]);
			$start = eventon_get_lang_formatted_timestr($format.' h:i:a', $formatted_unix_s);

			return array(
				'start'=> $start,// this didnt work on tickets addon
				'start_'=> date($format.' h:i:a',$intervals[$repeat_interval][0]),
				'end'=> date($format.' h:i:a',$intervals[$repeat_interval][1]),
			);

		}else{// no repeat interval values saved
			$start = !empty($post_meta['evcal_srow'])? date($format.' h:i:a', $post_meta['evcal_srow'][0]) :0;
			return array(
				'start'=> $start,
				'end'=> ( !empty($post_meta['evcal_erow'])? date($format.' h:i:a',$post_meta['evcal_erow'][0]): $start)
			);
		}
	}

	// return just UNIX timestamps corrected for repeat intervals
	public function get_correct_event_repeat_time($post_meta, $repeat_interval=''){
		if(!empty($repeat_interval) && !empty($post_meta['repeat_intervals']) && $repeat_interval!='0'){
			$intervals = unserialize($post_meta['repeat_intervals'][0]);

			return array(
				'start'=> $intervals[$repeat_interval][0],
				'end'=> $intervals[$repeat_interval][1],
			);

		}else{// no repeat interval values saved
			$start = !empty($post_meta['evcal_srow'])? $post_meta['evcal_srow'][0] :0;
			return array(
				'start'=> $start,
				'end'=> ( !empty($post_meta['evcal_erow'])? $post_meta['evcal_erow'][0]: $start)
			);
		}
	}

	// return a smarter complete date-time -translated and formatted to date-time string
	// 2.3.13
	public function get_formatted_smart_time($startunix, $endunix, $epmv){

		$wp_time_format = get_option('time_format');
		$wp_date_format = get_option('date_format');

		$start_ar = eventon_get_formatted_time($startunix);
		$end_ar = eventon_get_formatted_time($endunix);
		$_is_allday = (!empty($epmv['evcal_allday']) && $epmv['evcal_allday'][0]=='yes')? true:false;

		$output = '';

		// reused
			$joint = ' - ';

		// same year
		if($start_ar['y']== $end_ar['y']){
			// same month
			if($start_ar['n']== $end_ar['n']){
				// same date
				if($start_ar['j']== $end_ar['j']){
					if($_is_allday){
						$output = $this->date($wp_date_format, $start_ar) .' ('.evo_lang_get('evcal_lang_allday','All Day').')';
					}else{
						$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint. $this->date($wp_time_format, $end_ar);
					}
				}else{// dif dates
					if($_is_allday){
						$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.$this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')';
					}else{
						$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint.$this->date($wp_date_format.' '.$wp_time_format, $end_ar);
					}
				}
			}else{// dif month
				if($_is_allday){
					$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.$this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')';
				}else{// not all day
					$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar).$joint.$this->date($wp_date_format.' '.$wp_time_format, $end_ar);
				}
			}
		}else{
			if($_is_allday){
				$output = $this->date($wp_date_format, $start_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')'.$joint.$this->date($wp_date_format, $end_ar).' ('.evo_lang_get('evcal_lang_allday','All Day').')';
			}else{// not all day
				$output = $this->date($wp_date_format.' '.$wp_time_format, $start_ar). $joint .$this->date($wp_date_format.' '.$wp_time_format, $end_ar);
			}
		}
		return $output;	
	}

	// return datetime string for a given format using date-time data array
		public function date($dateformat, $array){		
			$items = str_split($dateformat);
			$newtime = '';
			foreach($items as $item){
				$newtime .= (array_key_exists($item, $array))? $array[$item]: $item;
			}
			return $newtime;
		} 

}