<?php

class MyZipWriter extends \ZipWriter
{

	public function __construct($strFile)
	{
		parent::__construct($strFile);
	}


	public function addStringUncompressed($strData, $strName, $intTime=0)
	{
		++$this->intCount;
		$strName = str_replace('\\', '/', $strName);

		// Start file
		$arrFile['file_signature']            = self::FILE_SIGNATURE;
		$arrFile['version_needed_to_extract'] = "\x14\x00";
		$arrFile['general_purpose_bit_flag']  = "\x00\x00";
		$arrFile['compression_method']        = "\x00\x00";
		$arrFile['last_mod_file_hex']         = $this->unixToHex($intTime);
		$arrFile['crc-32']                    = pack('V', crc32($strData));

		$intUncompressed = strlen($strData);
		$intCompressed = strlen($strData);

		// Continue file
		$arrFile['compressed_size']           = pack('V', $intCompressed);
		$arrFile['uncompressed_size']         = pack('V', $intUncompressed);
		$arrFile['file_name_length']          = pack('v', strlen($strName));
		$arrFile['extra_field_length']        = "\x00\x00";
		$arrFile['file_name']                 = $strName;
		$arrFile['extra_field']               = '';

		// Store file offset
		$intOffset = @ftell($this->resFile);

		// Add file to archive
		@fputs($this->resFile, implode('', $arrFile));
		@fputs($this->resFile, $strData);

		// Start central directory
		$arrHeader['header_signature']          = self::CENTRAL_DIR_START;
		$arrHeader['version_made_by']           = "\x00\x00";
		$arrHeader['version_needed_to_extract'] = $arrFile['version_needed_to_extract'];
		$arrHeader['general_purpose_bit_flag']  = $arrFile['general_purpose_bit_flag'];
		$arrHeader['compression_method']        = $arrFile['compression_method'];
		$arrHeader['last_mod_file_hex']         = $arrFile['last_mod_file_hex'];
		$arrHeader['crc-32']                    = $arrFile['crc-32'];
		$arrHeader['compressed_size']           = $arrFile['compressed_size'];
		$arrHeader['uncompressed_size']         = $arrFile['uncompressed_size'];
		$arrHeader['file_name_length']          = $arrFile['file_name_length'];
		$arrHeader['extra_field_length']        = $arrFile['extra_field_length'];
		$arrHeader['file_comment_length']       = "\x00\x00";
		$arrHeader['disk_number_start']         = "\x00\x00";
		$arrHeader['internal_file_attributes']  = "\x00\x00";
		$arrHeader['external_file_attributes']  = pack('V', 32);
		$arrHeader['offset_of_local_header']    = pack('V', $intOffset);
		$arrHeader['file_name']                 = $arrFile['file_name'];
		$arrHeader['extra_field']               = $arrFile['extra_field'];
		$arrHeader['file_comment']              = '';

		// Add entry to central directory
		$this->strCentralDir .= implode('', $arrHeader);
	}

}