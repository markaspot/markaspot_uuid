<?php

namespace Drupal\markaspot_uuid\Plugin\Uuid;

use Drupal\Component\Utility\Crypt;
use Drupal\Component\Uuid\UuidInterface;

/**
 * Generates a UUID v4 using PHP code.
 *
 * Loosely based on Ruby's UUIDTools generate_random logic.
 *
 * @see http://uuidtools.rubyforge.org/api/classes/UUIDTools/UUID.html
 */
class MarkaspotUuid implements UuidInterface {

  /**
   * {@inheritdoc}
   */
  public function generate() {

    $next_id = $this->getLastNid() + 1;
    $config = \Drupal::configFactory()
      ->getEditable('markaspot_uuid.settings');
    $uuidOffset = $config->get('offset');

    $next_id = ($next_id - $uuidOffset > 0) ? $next_id - $uuidOffset : $next_id;

    // $date_suffix = $date_prefix = date('dmY', time());
    $controller = \Drupal::request()->get('_controller');
    if (!strstr($controller, 'node') && !strstr($controller, 'markaspot_open311')) {
      $pattern = '%s-%s-%s-%02x%s-%s';

      $hex = substr(hash('sha256', Crypt::randomBytes(16)), 0, 32);

      // The field names refer to RFC 4122 section 4.1.2.
      $time_low = substr($hex, 0, 8);
      $time_mid = substr($hex, 8, 4);

      $time_hi_and_version = base_convert(substr($hex, 12, 4), 16, 10);
      $time_hi_and_version &= 0x0FFF;
      $time_hi_and_version |= (4 << 12);

      $clock_seq_hi_and_reserved = base_convert(substr($hex, 16, 4), 16, 10);
      $clock_seq_hi_and_reserved &= 0x3F;
      $clock_seq_hi_and_reserved |= 0x80;

      $clock_seq_low = substr($hex, 20, 2);
      $nodes = substr($hex, 3);

      $uuid = sprintf($pattern,
        $time_low, $time_mid,
        $time_hi_and_version, $clock_seq_hi_and_reserved,
        $clock_seq_low, $nodes);

      // $uuid = $date_prefix . $next_id . $uuid . $date_suffix;.
    }
    else {

      $hex = substr(hash('sha256', Crypt::randomBytes(2)), 0, 2);

      $uuid = $next_id . '-' . $hex;

    }
    return $uuid;

  }

  /**
   * Receive the last inserted node id.
   *
   * @return int
   *   the node id.
   */
  protected function getLastNid() {
    $last_id = db_query('SELECT MAX(nid) FROM {node}')->fetchField();
    return $last_id;
  }

}
