<?php
/**
 * This file is part of the Tmdb PHP API created by Michael Roterman.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Tmdb
 * @author Michael Roterman <michael@wtfz.net>
 * @copyright (c) 2013, Michael Roterman
 * @version 0.0.1
 */
namespace Tmdb\Model\Tv\QueryParameter;

use Tmdb\Model\Common\QueryParameter\AppendToResponse as BaseAppendToResponse;

/**
 * Class AppendToResponse
 * @package Tmdb\Model\Tv\QueryParameter
 */
class AppendToResponse extends BaseAppendToResponse
{
    const CREDITS       = 'credits';
    const EXTERNAL_IDS  = 'external_ids';
    const IMAGES        = 'images';
    const TRANSLATIONS  = 'translations';
    const VIDEOS        = 'videos';
}
