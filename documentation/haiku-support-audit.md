# Haiku OS Support Audit - Task List

This document outlines the remaining tasks required to achieve full, robust support for Haiku OS within the Phoronix Test Suite.

## 1. Hardware Detection Enhancements (Phodevi)

- [ ] **Motherboard/BIOS Detection:** Implement logic in `phodevi_motherboard.php` to identify the motherboard model and BIOS version on Haiku. Investigate if `sysinfo` or a Haiku port of `dmidecode` can provide this.
- [ ] **GPU Driver Detection:** Enhance `phodevi_gpu.php` to detect the active graphics driver (e.g., intel_extreme, radeon, nouveau, or the VESA/framebuffer fallback).
- [ ] **Monitor Information:** Implement monitor/EDID detection for Haiku.

## 2. Hardware Sensors

- [ ] **Thermal Monitoring:** Implement sensors for CPU and GPU temperatures.
- [ ] **Fan Speed & Voltage:** Implement sensors for system fans and voltages.
- [ ] **Power Consumption:** Investigate if battery/ACPI power consumption can be reported.

## 3. Test Profile Compatibility

- [ ] **SupportedPlatforms Update:** Audit popular test profiles (e.g., `pts/sqlite`, `pts/git`, `pts/compress-7zip`, `pts/build-php`) and add `Haiku` to their `test-definition.xml` files.
- [ ] **Dependency Mapping:** Continue expanding `haiku-packages.xml` to map more generic dependencies to Haiku `pkgman` packages.
- [ ] **Build Fixes:** Fix common compilation issues on Haiku (e.g., missing `/proc` assumptions, POSIX shared memory differences).

## 4. System Operations

- [ ] **Reboot/Shutdown:** Verify and potentially refine the `reboot` and `shutdown` commands for Haiku in `phodevi.php`.
- [ ] **Uptime:** Ensure `system_uptime` is accurate across different Haiku revisions.

## 5. Benchmarking Features

- [ ] **Process Isolation/Priority:** Ensure PTS can correctly manage process priorities on Haiku for consistent benchmarking results.
- [ ] **Disk Scheduler Detection:** Implement detection of the Haiku I/O scheduler.
