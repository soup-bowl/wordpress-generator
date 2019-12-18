<?php declare(strict_types=1);
/**
 * WordPress development container generator.
 *
 * @package twpg-wordpress-generator
 * @author BRE Digital
 * @license GPL-3.0
 */

namespace TWPG\Models;

use TWPG\Models\Models;
use Carbon\Carbon;

/**
 * Communicates with the sitelog table, the only table created by and for this tool.
 */
class Sitelog extends Models {
	public function get( int $site_id ):array {
		$stmt = $this->PDO_ALL->prepare(
			"SELECT * FROM {$this->config->database->maintable} where id = ?"
		);
		$stmt->execute( [ $site_id ] );
		$arr = $stmt->fetch();

		return $arr;
	}

	public function getAll( bool $showDeleted = true ):array {
		if ( $showDeleted )
			$stmt = $this->PDO_ALL->query( 'SELECT * FROM ' . $this->config->database->maintable );
		else
			$stmt = $this->PDO_ALL->query( 'SELECT * FROM ' . $this->config->database->maintable . ' where deleted_date is null' );

		$setColl = [];
		while ( $row = $stmt->fetch() ) {
			array_push( $setColl, $row );
		}

		return $setColl;
	}

	public function create( string $name = null, string $ipAddr = null, bool $secure = false ):string {
		$this->PDO_ALL->prepare(
			"INSERT INTO {$this->config->database->maintable} (name, secure, created_by, created_date) VALUES (?, ?, ?, ?)"
		)->execute( [ $name, (int) $secure, $ipAddr, Carbon::now()->toDateTimeString() ] );

		return $this->PDO_ALL->lastInsertId();
	}

	public function purge( int $id, string $ipAddr ):void {
		$tablenames = $this->tables( $id );

		if ( count( $tablenames ) !== 0 ) {
			$this->PDO_ALL->query( 'DROP TABLE ' . implode( ',', $tablenames ) );
		}

		$this->PDO_ALL->prepare(
			'UPDATE wpmgr_sitelog SET deleted_date = ?, deleted_by = ? WHERE id = ?'
		)->execute( [ Carbon::now()->toDateTimeString(), $ipAddr, $id ] );
	}

	public function extendtime( int $id, int $days ):void {
		$this->PDO_ALL->prepare(
			'UPDATE wpmgr_sitelog SET extensiondays = extensiondays + ? WHERE id = ?'
		)->execute( [ $days, $id ] );
	}

	public function getReminderStatus( int $id ):bool {
		$stmt = $this->PDO_ALL->prepare(
			"SELECT emailreminder FROM {$this->config->database->maintable} WHERE id = :id"
		);
		$stmt->bindParam( ':id', $id );
		$stmt->execute();
		return filter_var( $stmt->fetch()['emailreminder'], FILTER_VALIDATE_BOOLEAN );
	}

	public function setReminderStatus( int $id, bool $blResp ):void {
		$this->PDO_ALL->prepare(
			"UPDATE {$this->config->database->maintable} SET emailreminder =  ? WHERE id = ?"
		)->execute( [ (int)$blResp, $id ] );
	}

	public function setProtectedStatus( int $id, bool $isProtected ):void {
		$this->PDO_ALL->prepare(
			"UPDATE {$this->config->database->maintable} SET protected =  ? WHERE id = ?"
		)->execute([ (int)$isProtected, $id ] );
	}
}
