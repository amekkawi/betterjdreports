<?php
/*
 * Copyright (c) 2011 André Mekkawi <betterjdreports@andremekkawi.com>
 *
 * LICENSE
 * This source file is subject to the MIT license in the file LICENSE.txt.
 * The license is also available at https://raw.github.com/amekkawi/betterjdreports/master/LICENSE.txt
 */

class V extends VCore
{
	static function Check($type, &$value, $returnMessage = false) {
		// Allow parent to check first.
		if (parent::Check($type, $value) == false) return false;
		
		$specialchars = '\x21-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E';
		$normalcharacters = 'A-Za-z0-9' . $specialchars;
		
		switch(strtolower($type)) {
			case 'uuid': 		return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $value) ? true : ($returnMessage ? 'Must be a valid UUID.' : false);
			case 'password':	return preg_match('/^.{1,32}$/', $value); //TODO change to {6,32}
			case 'date':		return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $value) ? true : ($returnMessage ? 'Must be a valid date in the format YYYY-MM-DD.' : false);
			case 'time':		return preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value) ? true : ($returnMessage ? 'Must be a valid time in the format HH:MM:SS.' : false);
			case 'datetime':	return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $value) ? true : ($returnMessage ? 'Must be a valid date time in the format YYYY-MM-DD HH:MM:SS.' : false);
			case 'boolean':		return preg_match('/^[01]$/', $value."") ? true : ($returnMessage ? 'Must be either a 0 or 1.' : false);
			case 'integer':		return preg_match('/^[0-9]+$/', $value.'') ? true : ($returnMessage ? 'Must be a whole number.' : false);
			case 'posinteger':	return $value.'' !== '0' && V::Check('integer', $value) ? true : ($returnMessage ? 'Must be a whole number greater than 0.' : false);
			case 'email':		return TRUE; // TODO: Add validation.
			case 'sortdir':		return $value == 'asc' || $value == 'desc' ? true : ($returnMessage ? 'Invalid sort direction.' : false);
			case 'array':		return is_array($value) ? true : ($returnMessage ? 'Invalid array.' : false);
			case 'stringarray':
				if (($ret = V::Check('array', $value, $returnMessage)) !== TRUE) {
					return $ret;
				}
				else {
					foreach ($value as $val) {
						if (!is_string($val)) return $returnMessage ? 'Invalid string array item.' : false;
					}
					return TRUE;
				}
			case 'string':		return is_string($value) ? true : ($returnMessage ? 'Invalid string.' : false);
			case 'bytes':		return preg_match('/^([0-9]+)(?:[ ]?([KMG])(?:[B]?))?$/i', $value) ? true : ($returnMessage ? 'Invalid byte format.' : false);
			case 'controller':	return Controller::ValidateControllerName($value) ? true : ($returnMessage ? 'Invalid controller name.' : false);
			case 'action':		return Controller::ValidateAction($value) ? true : ($returnMessage ? 'Invalid action.' : false);
			case 'unixtime':	return V::Check('integer', $value) ? true : ($returnMessage ? 'Invalid UNIX time.' : false);
			
			/* Latest search */
			case 'latestsearch':
				if (is_string($value) && preg_match('/^(clientid|job|reporttime|result|errors)-([a-z])-(.*)$/', $value, $matches)) {
					return V::Check('searchfilters', $value, $returnMessage);
				}
				else {
					return $returnMessage ? 'Invalid latestsearch string.' : false;
				}
				break;
			
			/* Clients search */
			case 'clientssearchsort': return $value == 'totalreports' || V::Check('table:clients', $value) ? true : ($returnMessage ? 'Invalid sort column name.' : false);
			case 'clientssearch':
				if (is_string($value) && preg_match('/^(clientid|totalreports|tags)-([a-z])-(.*)$/', $value, $matches)) {
					switch ($matches[1]) {
						case 'totalreports':
							return V::Check('integer', $matches[3], $returnMessage);
						case 'tags':
							if ($matches[3] == '') return TRUE;
							return V::Check('table:tags:tag', $matches[3], $returnMessage);
						
						default:
							return V::Check('searchfilters', $value, $returnMessage);
					}
				}
				else {
					return $returnMessage ? 'Invalid clientssearch string.' : false;
				}
				break;
			
			/* Reports search */
			case 'reportssearch':
				if (is_string($value) && preg_match('/^(clientid|job|reporttime|duration|uploadsize|changedfiles|result|errors)-([a-z])-(.*)$/', $value, $matches)) {
					return V::Check('searchfilters', $value, $returnMessage);
				}
				else {
					return $returnMessage ? 'Invalid reportssearch string.' : false;
				}
				break;
			
			/* Items search */
			case 'itemssearch':
				if (is_string($value) && preg_match('/^(logorder|type|path|size2|starttime|duration|result|detail)-([a-z])-(.*)$/', $value, $matches)) {
					return V::Check('searchfilters', $value, $returnMessage);
				}
				else {
					return $returnMessage ? 'Invalid itemssearch string.' : false;
				}
				break;
			
			/* File search */
			case 'filesearchpath': return is_string($value) && strlen($value) > 3 ? true : ($returnMessage ? 'Invalid file search path.' : false);
			case 'filesearch':
				if (is_string($value) && preg_match('/^(path|job|reporttime|type|size2|duration|result|detail)-([a-z])-(.*)$/', $value, $matches)) {
					return V::Check('searchfilters', $value, $returnMessage);
				}
				else {
					return $returnMessage ? 'Invalid itemssearch string.' : false;
				}
				break;
			
			/* Validation of search filter values */
			case 'searchfilters':
				if (is_string($value) && preg_match('/^([a-z0-9]+)-([a-z])-(.*)$/', $value, $matches)) {
					switch ($matches[1]) {
						//case 'path': return strlen($matches[3].'') <= 255 * 3 ? true : ($returnMessage ? 'TODO' : false);
						
						case 'duration': return preg_match('/^([0-9]+|((([0-9]{1,2}:)?[0-9]{1,2}:)?[0-9]{1,2}))$/', $matches[3]) ? true : ($returnMessage ? 'Invalid duration format.' : false);
						case 'starttime': return preg_match('/^[0-9]+|(([0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2} )?[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})$/', $matches[3]) ? true : ($returnMessage ? 'Invalid report time format.' : false);
						case 'reporttime': return preg_match('/^[0-9]+|([0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}( [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})?)$/', $matches[3]) ? true : ($returnMessage ? 'Invalid report time format.' : false);
						
						case 'size2':
						case 'uploadsize':
							return V::Check('bytes', $matches[3], $returnMessage);
						
						case 'changedfiles':
						case 'errors':
						case 'logorder':
						case 'type':
							return V::Check('integer', $matches[3], $returnMessage);
						
						default:
							return true;
					}
				}
				else {
					return $returnMessage ? 'Invalid itemssearch string.' : false;
				}
				break;
			
			/* Table: users */
			case 'table:users':				return V::_CheckTableCols($type, 'userid|name|password|email|timezone|created|modified', $value);
			case 'table:users:userid':		return preg_match('/^.{3,50}$/', $value) ? true : ($returnMessage ? 'Invalid user ID.' : false);
			case 'table:users:name':		return preg_match('/^.{1,100}$/', $value) ? true : ($returnMessage ? 'Invalid user name.' : false);
			case 'table:users:password':	return true; // Encrypted, so cannot validate.
			case 'table:users:email':		return $value == '' || V::Check('email', $value, $returnMessage);
			case 'table:users:timezone':	return $value == '' || true; // TODO: Possible to verify?
			case 'table:users:created':		return V::Check('datetime', $value, $returnMessage);
			case 'table:users:modified':	return V::Check('datetime', $value, $returnMessage);
			
			/* Table: reports */
			case 'table:reports':			return V::_CheckTableCols($type, 'clientid|job|reporttime|othertime|duration|disk|totalfiles|totalsize|changedfiles|uploadsize|errors|result', $value);
			case 'table:reports:clientid':	return V::Check('table:clients:clientid', $value, $returnMessage);
			case 'table:reports:job':		return preg_match('/^.{1,100}$/', $value) ? true : ($returnMessage ? 'Invalid job name.' : false);
			case 'table:reports:reporttime':return V::Check('unixtime', $value, $returnMessage);
			
			/* Table: clients */
			case 'table:clients':				return V::_CheckTableCols($type, 'clientid', $value);
			case 'table:clients:clientid':		return preg_match('/^.{3,36}$/', $value) ? true : ($returnMessage ? 'Invalid client ID' : false);
			
			case 'table:items':				return V::_CheckTableCols($type, 'clientid|reporttime|logorder|type|disk|path1|path2|path3|size1|size2|starttime|duration|result|detail', $value);
			
			case 'table:tags:tag':			return preg_match('/^.{1,15}$/', $value) ? true : ($returnMessage ? 'Invalid user name.' : false);
			
			case 'table:bookmarks':				return V::_CheckTableCols($type, 'name|userid|controller|action|arguments|querystring', $value);
			case 'table:bookmarks:name':		return is_string($value) && strlen($value) <= 100 ? true : ($returnMessage ? 'Invalid bookmark name.' : false);
			case 'table:bookmarks:userid':		return V::Check('table:users:userid', $value, $returnMessage);
			case 'table:bookmarks:controller':	return V::Check('controller', $value) && strlen($value) <= 15 ? true : ($returnMessage ? 'Invalid controller name.' : false);
			case 'table:bookmarks:action':		return V::Check('action', $value) && strlen($value) <= 15 ? true : ($returnMessage ? 'Invalid action name.' : false);
			case 'table:bookmarks:arguments':	return V::Check('string', $value, $returnMessage);
			case 'table:bookmarks:querystring':	return V::Check('string', $value, $returnMessage);
			
			case 'table:history':				return V::_CheckTableCols($type, 'target|id1|id2|eventtime|action|userid|description', $value);
			case 'table:history:userid':		return V::Check('table:users:userid', $value, $returnMessage);
			case 'table:history:eventtime':		return V::Check('unixtime', $value, $returnMessage);
			case 'table:history:target':
			case 'table:history:id1':
			case 'table:history:id2':
			case 'table:history:action':
			case 'table:history:description':
				return TRUE;
			
			case 'table:metadata':			return V::_CheckTableCols($type, 'clientid|type|data', $value);
			case 'table:metadata:clientid':	return V::Check('table:clients:clientid', $value, $returnMessage);
			case 'table:metadata:type':		return TRUE; // TODO
			case 'table:metadata:data':		return TRUE; // TODO
			
			default:
				break;
		}
		
		trigger_error('Bad $type passed to V::Check(): "' . htmlspecialchars($type). '"', E_USER_ERROR);
		return true;
	}
	
	static function _CheckTableCols($type, $validcols, $value) {
		return !is_array($value) ? preg_match('/^('.$validcols.')$/', strtolower($value)) || $value == "*" : V::_CheckTableArray($type, $value);
	}
	
	static function _CheckTableArray($type, $array) {
		foreach ($array as $item) {
			// It is a list of columns
			if (is_string($item)) {
				if (!V::Check($type, $item)) return false;
			}
			
			// It is a list of columns for an ORDER clause.
			elseif (!isset($item['column']) && is_string($item['column'])) {
				if (!V::Check($type, $item['column'])) return false;
			}
			// Not a valid array of columns
			else {
				return false;
			}
		}
		
		// All items in the array are valid.
		return true;
	}
}

?>