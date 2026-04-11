<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2010 - 2021, Phoronix Media
	Copyright (C) 2010 - 2021, Michael Larabel
	phodevi_audio.php: The PTS Device Interface object for audio / sound cards

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class phodevi_audio extends phodevi_device_interface
{
	public static function properties()
	{
		return array(
			'identifier' => new phodevi_device_property('audio_processor_string', phodevi::smart_caching)
		);
	}
	public static function audio_processor_string()
	{
		$audio = null;

		if(phodevi::is_macos())
		{
			// TODO: implement
		}
		else if(phodevi::is_haiku())
		{
			$listdev = phodevi_haiku_parser::read_listdev('/Multimedia audio controller/i');
			if(is_array($listdev) && isset($listdev[0]))
			{
				$info = $listdev[0];
				$lines = explode(PHP_EOL, $info);
				$vendor = '';
				$device = '';
				foreach($lines as $line)
				{
					$line_t = trim($line);
					if(strpos($line_t, 'vendor ') === 0 && strpos($line_t, ':') !== false)
					{
						$vendor = trim(substr($line_t, strpos($line_t, ':') + 1));
					}
					else if(strpos($line_t, 'device ') === 0 && strpos($line_t, ':') !== false)
					{
						$device = trim(substr($line_t, strpos($line_t, ':') + 1));
					}
				}

				if(!empty($vendor) || !empty($device))
				{
					$audio = trim($vendor . ' ' . $device);
				}
				else
				{
					$audio = trim(preg_replace('/device Multimedia audio controller\s+\[[^\]]+\]/i', '', $info));
				}
				$audio = str_replace(array('[', ']', 'Multimedia audio controller'), '', $audio);
				$audio = pts_strings::trim_spaces(str_replace('  ', ' ', $audio));
			}
		}
		else if(phodevi::is_bsd())
		{
			foreach(array('dev.hdac.0.%desc') as $dev)
			{
				$dev = phodevi_bsd_parser::read_sysctl($dev);

				if(!empty($dev))
				{
					$audio = $dev;
				}
			}
		}
		else if(phodevi::is_windows())
		{
			$win_sound = array();
			$win32_sounddevice = shell_exec('powershell -NoProfile "(Get-WMIObject -Class win32_sounddevice | Select Name)"');
			if(($x = strpos($win32_sounddevice, '----')) !== false)
			{
				$win32_sounddevice = trim(substr($win32_sounddevice, $x + 4));
				foreach(explode("\n", $win32_sounddevice) as $sd)
				{
					if(!empty($sd))
					{
						$win_sound[] = $sd;
					}
				}
			}
			$win_sound = array_unique($win_sound);
			$audio = implode(' + ', $win_sound);
		}
		else if(phodevi::is_linux())
		{
			foreach(pts_file_io::glob('/sys/class/sound/card*/hwC0D*/vendor_name') as $vendor_name)
			{
				$card_dir = dirname($vendor_name) . '/';

				if(!is_readable($card_dir . 'chip_name'))
				{
					continue;
				}


				$vendor_name = pts_file_io::file_get_contents($vendor_name);
				$chip_name = pts_file_io::file_get_contents($card_dir . 'chip_name');

				$audio = $vendor_name . ' '. $chip_name;

				if(strpos($chip_name, 'HDMI') !== false || strpos($chip_name, 'DP') !== false)
				{
					// If HDMI is in the audio string, likely the GPU-provided audio, so try to find the mainboard otherwise
					$audio = null;
				}
				else
				{
					break;
				}
			}

			if($audio == null)
			{
				$audio = phodevi_linux_parser::read_pci('Multimedia audio controller');
			}

			if($audio == null)
			{
				$audio = phodevi_linux_parser::read_pci('Audio device');
			}
		}

		return $audio;
	}
}

?>
