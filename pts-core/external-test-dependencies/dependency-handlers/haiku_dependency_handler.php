<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2015 - 2024, Phoronix Media
	Copyright (C) 2015 - 2024, Michael Larabel

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

class haiku_dependency_handler implements pts_dependency_handler
{
	public static function startup_handler()
	{
		// Nothing to do
	}
	public static function what_provides($files_needed)
	{
		if(!pts_client::executable_in_path('pkgman'))
		{
			return array();
		}

		$packages_needed = array();
		foreach(pts_arrays::to_array($files_needed) as $file)
		{
			$pkgman_search = self::run_pkgman_search($file);
			if($pkgman_search != null)
			{
				$packages_needed[$file] = $pkgman_search;
			}
			else
			{
				// Try common paths
				foreach(array('/boot/system/bin/', '/boot/system/non-packaged/bin/') as $possible_path)
				{
					$pkgman_search = self::run_pkgman_search($possible_path . $file);
					if($pkgman_search != null)
					{
						$packages_needed[$file] = $pkgman_search;
						break;
					}
				}
			}
		}
		return $packages_needed;
	}
	protected static function run_pkgman_search($arg)
	{
		// pkgman search -f <file> returns packages providing that file
		$pkgman_output = shell_exec('pkgman search -f ' . escapeshellarg($arg) . ' 2>/dev/null');

		if(empty($pkgman_output))
		{
			return null;
		}

		// Parse pkgman output
		// Format is typically:
		// Status  Name  Description
		// -------------------------
		// ...
		$lines = explode(PHP_EOL, $pkgman_output);
		foreach($lines as $line)
		{
			if(empty($line) || strpos($line, '---') !== false || stripos($line, 'Status') !== false)
			{
				continue;
			}

			// pkgman search -f <file> output format:
			// S  Name  Description
			// -------------------
			// [status] [name] [description]
			//
			// If uninstalled, the status column is empty, but we must account for the column width.
			// The columns are: Status (2 chars), Name (variable), Description (variable)
			// A safer way is to check the first few characters.

			$status = substr($line, 0, 2);
			$line_rest = trim(substr($line, 2));
			$parts = preg_split('/\s+/', $line_rest);

			if(count($parts) >= 1)
			{
				$package_name = $parts[0];
				// Remove version if present (e.g. name-version-revision)
				// Haiku package names can contain dashes, but versions usually start with a digit after a dash
				while(($x = strrpos($package_name, '-')) !== false && isset($package_name[$x + 1]) && is_numeric($package_name[$x + 1]))
				{
					$package_name = substr($package_name, 0, $x);
				}
				return $package_name;
			}
		}

		return null;
	}
	public static function install_dependencies($os_packages_to_install)
	{
		// Not needed since this OS uses a dependency install script (install-haiku-packages.sh) instead...
	}
}

?>
