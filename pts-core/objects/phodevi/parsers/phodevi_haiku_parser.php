<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009 - 2024, Phoronix Media
	Copyright (C) 2009 - 2024, Michael Larabel

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

class phodevi_haiku_parser
{
	public static function read_sysinfo($grep = null)
	{
		$info = null;

		if(pts_client::executable_in_path('sysinfo'))
		{
			$output = shell_exec('sysinfo 2> /dev/null');

			if(!empty($output))
			{
				if($grep == null)
				{
					$info = $output;
				}
				else
				{
					$lines = explode(PHP_EOL, $output);
					foreach($lines as $line)
					{
						if(stripos($line, $grep) !== false)
						{
							if(!is_array($info)) $info = array();
							$info[] = trim($line);
						}
						// Support regex matching
						else if(@preg_match($grep, $line))
						{
							if(!is_array($info)) $info = array();
							$info[] = trim($line);
						}
					}
				}
			}
		}

		return $info;
	}

	public static function read_listdev($grep = null)
	{
		$info = null;

		if(pts_client::executable_in_path('listdev'))
		{
			$output = shell_exec('listdev 2> /dev/null');

			if(!empty($output))
			{
				if($grep == null)
				{
					$info = $output;
				}
				else
				{
					// listdev outputs blocks starting with "device " at the beginning of a line
					// followed by properties indented with spaces.
					$blocks = preg_split('/^device /m', $output);
					foreach($blocks as $block)
					{
						$block = trim($block);
						if(empty($block)) continue;

						if(stripos($block, $grep) !== false)
						{
							if(!is_array($info)) $info = array();
							$info[] = trim('device ' . $block);
						}
						else if(@preg_match($grep, $block))
						{
							if(!is_array($info)) $info = array();
							$info[] = trim('device ' . $block);
						}
					}
				}
			}
		}

		return $info;
	}
}

?>
