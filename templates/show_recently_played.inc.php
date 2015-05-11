<?php
/* vim:set softtabstop=4 shiftwidth=4 expandtab: */
/**
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright 2001 - 2015 Ampache.org
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

$link = AmpConfig::get('use_rss') ? ' ' . Ampache_RSS::get_display('recently_played') :  '';
UI::show_box_top(T_('Recently Played') . $link, 'box box_recently_played');
?>
<table class="tabledata" cellpadding="0" cellspacing="0">
    <thead>
        <tr class="th-top">
            <th class="cel_play"></th>
            <th class="cel_song"><?php echo T_('Song'); ?></th>
            <th class="cel_add"></th>
            <th class="cel_album"><?php echo T_('Album'); ?></th>
            <th class="cel_artist"><?php echo T_('Artist'); ?></th>
            <th class="cel_username"><?php echo T_('Username'); ?></th>
            <th class="cel_lastplayed"><?php echo T_('Last Played'); ?></th>
            <?php if (Access::check('interface', 50)) { ?>
            <th class="cel_agent"><?php echo T_('Agent'); ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
<?php
$nb = 0;
foreach ($data as $row) {
    $row_user = new User($row['user']);
    $song = new Song($row['object_id']);

    $agent = '';
    $time_string = '-';

    $has_allowed_agent = true;
    $has_allowed_time = true;
    $is_allowed = Access::check('interface', '100') || $GLOBALS['user']->id == $row_user->id;
    if (!$is_allowed) {
        $has_allowed_time = Preference::get_by_user($row_user->id, 'allow_personal_info_time');
        $has_allowed_agent = Preference::get_by_user($row_user->id, 'allow_personal_info_agent');
    }

    if ($is_allowed || $has_allowed_agent) {
        $agent = $row['agent'];
        if (!empty($row['geo_name'])) {
            $agent .= ' - ' . $row['geo_name'];
        }
    }

    if ($is_allowed || $has_allowed_time) {
        $interval = intval(time() - $row['date']);

        if ($interval < 60) {
            $time_string = sprintf(ngettext('%d second ago', '%d seconds ago', $interval), $interval);
        } elseif ($interval < 3600) {
            $interval = floor($interval / 60);
            $time_string = sprintf(ngettext('%d minute ago', '%d minutes ago', $interval), $interval);
        } elseif ($interval < 86400) {
            $interval = floor($interval / 3600);
            $time_string = sprintf(ngettext('%d hour ago', '%d hours ago', $interval), $interval);
        } elseif ($interval < 604800) {
            $interval = floor($interval / 86400);
            $time_string = sprintf(ngettext('%d day ago', '%d days ago', $interval), $interval);
        } elseif ($interval < 2592000) {
            $interval = floor($interval / 604800);
            $time_string = sprintf(ngettext('%d week ago', '%d weeks ago', $interval), $interval);
        } elseif ($interval < 31556926) {
            $interval = floor($interval / 2592000);
            $time_string = sprintf(ngettext('%d month ago', '%d months ago', $interval), $interval);
        } elseif ($interval < 631138519) {
            $interval = floor($interval / 31556926);
            $time_string = sprintf(ngettext('%d year ago', '%d years ago', $interval), $interval);
        } else {
            $interval = floor($interval / 315569260);
            $time_string = sprintf(ngettext('%d decade ago', '%d decades ago', $interval), $interval);
        }
    }
    $song->format();
?>
    <tr class="<?php echo UI::flip_class(); ?>">
        <td class="cel_play">
            <span class="cel_play_content">&nbsp;</span>
            <div class="cel_play_hover">
            <?php if (AmpConfig::get('directplay')) { ?>
                <?php echo Ajax::button('?page=stream&action=directplay&object_type=song&object_id=' . $song->id,'play', T_('Play'),'play_song_' . $nb . '_' . $song->id); ?>
                <?php if (Stream_Playlist::check_autoplay_append()) { ?>
                    <?php echo Ajax::button('?page=stream&action=directplay&object_type=song&object_id=' . $song->id . '&append=true','play_add', T_('Play last'),'addplay_song_' . $nb . '_' . $song->id); ?>
                <?php } ?>
                <?php if (Stream_Playlist::check_autoplay_next()) { ?>
                    <?php echo Ajax::button('?page=stream&action=directplay&object_type=song&object_id=' . $song->id . '&playnext=true', 'play_next', T_('Play next'), 'nextplay_song_' . $nb . '_' . $song->id); ?>
                <?php } ?>
        <?php } ?>
            </div>
        </td>
        <td class="cel_song"><?php echo $song->f_link; ?></td>
        <td class="cel_add">
            <span class="cel_item_add">
                <?php echo Ajax::button('?action=basket&type=song&id='.$song->id, 'add', T_('Add to temporary playlist'), 'add_' . $nb . '_'.$song->id); ?>
                <a id="<?php echo 'add_playlist_' . $nb . '_'.$song->id ?>" onclick="showPlaylistDialog(event, 'song', '<?php echo $song->id ?>')">
                    <?php echo UI::get_icon('playlist_add', T_('Add to existing playlist')); ?>
                </a>
            </span>
        </td>
        <td class="cel_album"><?php echo $song->f_album_link; ?></td>
        <td class="cel_artist"><?php echo $song->f_artist_link; ?></td>
        <td class="cel_username">
            <a href="<?php echo AmpConfig::get('web_path'); ?>/stats.php?action=show_user&amp;user_id=<?php echo scrub_out($row_user->id); ?>">
            <?php echo scrub_out($row_user->fullname); ?>
            </a>
        </td>
        <td class="cel_lastplayed"><?php echo $time_string; ?></td>
        <?php if (Access::check('interface', 50)) { ?>
        <td class="cel_agent">
            <?php if (!empty($agent)) {
                echo UI::get_icon('info', $agent);
            } ?>
        </td>
        <?php } ?>
    </tr>
<?php
    ++$nb;
}
?>
<?php if (!count($data)) { ?>
    <tr>
        <td colspan="8"><span class="nodata"><?php echo T_('No recently item found'); ?></span></td>
    </tr>
<?php } ?>
    </tbody>
    <tfoot>
        <tr class="th-bottom">
            <th class="cel_play"></th>
            <th class="cel_song"><?php echo T_('Song'); ?></th>
            <th class="cel_add"></th>
            <th class="cel_album"><?php echo T_('Album'); ?></th>
            <th class="cel_artist"><?php echo T_('Artist'); ?></th>
            <th class="cel_username"><?php echo T_('Username'); ?></th>
            <th class="cel_lastplayed"><?php echo T_('Last Played'); ?></th>
            <?php if (Access::check('interface', 50)) { ?>
            <th class="cel_agent"><?php echo T_('Agent'); ?></th>
            <?php } ?>
        </tr>
    </tfoot>
</table>
<div id="recent_more">
<?php
    $user_id_a = '';
    if (isset($user_id) && !empty($user_id)) {
        $user_id_a = "&amp;user_id=" . scrub_out($user_id);
    }
?>
    <a href="<?php echo AmpConfig::get('web_path'); ?>/stats.php?action=recent<?php echo $user_id_a; ?>"><?php echo T_('More'); ?></a>
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function () {
    $("a[rel^='prettyPhoto']").prettyPhoto({social_tools:false});
});
</script>
<?php UI::show_box_bottom(); ?>
