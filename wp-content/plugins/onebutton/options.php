<?php if (false == empty($message)) : ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<?php
    $lut = array(
        'blink'         => __('Blink', 'onebutton'),
        'delicious'     => __('del.ici.ous', 'onebutton'),
        'digg'          => __('Digg', 'onebutton'),
        'furl'          => __('Furl', 'onebutton'),
        'google'        => __('Google', 'onebutton'),
        'simpy'         => __('Simpy', 'onebutton'),
        'spurl'         => __('Spurl', 'onebutton'),
        'ymyweb'        => __('Y! MyWeb', 'onebutton'),
        'bobrdobr'      => __('BobrDobr', 'onebutton'),
        'mrwong'        => __('Mr. Wong', 'onebutton'),
        'yabm'          => __('Yandex.Bookmarks', 'onebutton'),
        'text20'        => __('Text 2.0', 'onebutton'),
        'news2'         => __('News2', 'onebutton'),
        'addscoop'      => __('AddScoop', 'onebutton'),
        'ruspace'       => __('RuSpace', 'onebutton'),
        'rumarkz'       => __('RUmarkz', 'onebutton'),
        'memory'        => __('Memori', 'onebutton'),
        'googlebm'      => __('Google Bookmarks', 'onebutton'),
        'pisali'        => __('Pisali', 'onebutton'),
        'smi2'          => __('SMI 2', 'onebutton'),
        'myplace'       => __('Moe Mesto', 'onebutton'),
        'bm100'         => __('100 Zakladok', 'onebutton'),
        'wow'           => __('Vaau!', 'onebutton'),
        'technorati'    => __('Technorati', 'onebutton'),
        'rucity'        => __('RuCity', 'onebutton'),
        'linkstore'     => __('LinkStore', 'onebutton'),
        'newsland'      => __('NewsLand', 'onebutton'),
        'lopas'         => __('Lopas', 'onebutton'),
        'liua'          => __('Bookmarks - IN.UA', 'onebutton'),
        'connotea'      => __('Connotea', 'onebutton'),
        'bibsonomy'     => __('Bibsonomy', 'onebutton'),
        'trucking'      => __('Trucking Bookmarks', 'onebutton'),
        'communizm'     => __('Communizm', 'onebutton'),
        'uca'           => __('UCA', 'onebutton'),
    );
?>
<div class="wrap">
    <h2><?php _e("OneButton Configration", "onebutton"); ?></h2>
    <form method="post" action="<?php echo wp_specialchars($_SERVER['REQUEST_URI']); ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="ob_nofollow"><?php _e("Add <code>nofollow</code> to the links", "onebutton"); ?></label></th>
                    <td><input value="1" type="checkbox" name="onebutton[nofollow]" id="ob_nofollow"<?php if ($this->options['nofollow']) : ?> checked="checked"<?php endif; ?>/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ob_newwindow"><?php _e("Add <code>target=_blank</code> to the links", "onebutton"); ?></label></th>
                    <td><input value="1" type="checkbox" name="onebutton[newwindow]" id="ob_newwindow"<?php if ($this->options['newwindow']) : ?> checked="checked"<?php endif; ?>/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ob_single"><?php _e("Show OneButton only for full posts and pages", "onebutton"); ?></label></th>
                    <td><input value="1" type="checkbox" name="onebutton[only_single]" id="ob_single"<?php if ($this->options['only_single']) : ?> checked="checked"<?php endif; ?>/></td>
                </tr>
                <tr>
                    <th scope="row"><label for="ob_icons"><?php _e("Show bookmark services' icons", "onebutton"); ?></label></th>
                    <td><input value="1" type="checkbox" name="onebutton[show_icons]" id="ob_icons"<?php if ($this->options['show_icons']) : ?> checked="checked"<?php endif; ?>/></td>
                </tr>
            </tbody>
        </table>
        <p>&nbsp;</p>
        <table class="form-table">
            <caption style="font-weight: bold; font-size: 1.2em; text-align: left;"><?php _e("Use these Bookmark Services:", "onebutton") ?></caption>
            <tbody>
<?php foreach ($lut as $service => $svc) : ?>
                <tr>
                    <th scope="row"><label for="ob_<?php echo $service ?>"><?php echo $svc ?></label></th>
                    <td><input value="1" type="checkbox" name="onebutton[services][<?php echo $service ?>]" "ob_<?php echo $service ?>"<?php if ($this->options['services'][$service]) : ?> checked="checked"<?php endif; ?>/></td>
                </tr>
<?php endforeach; ?>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="update_options" value="<?php _e('Update Options', "onebutton"); ?>"/>
        </p>
    </form>
</div>
