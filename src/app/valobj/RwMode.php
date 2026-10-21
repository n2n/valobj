<?php
namespace valobj;

enum RwMode: string {
	case READ_ONLY = 'read_only';
	case WRITE_ONLY = 'write_only';
	case READ_WRITE = 'read_write';
}
