function JWP6Media() {' + t.JWP6 + '

    var $ = jQuery;

    var t = this;

    this.JWP6 = 'jwp6_';

    this.SELECT2_SETTINGS = {
        'minimumResultsForSearch': 8,
        'formatResult': function (opt) {
            var thumb = $(opt.element).data('thumb'), style;
            if (thumb) {
                style = (thumb.length > 10) ? ' style="background-image: url(' + thumb + ');"' : '';
                return '<span class="thumbedoption"><span class="thumboption"' + style + '></span>' + opt.text + '</span>';
            }
            return opt.text;
        }
    };

    this.fieldset_toggles = {
        'mediaid' : {
            'show' : ['mediaid_group', 'image_yesno_group'],
            'hide' : ['file_group', 'playlistid_group', 'imageid_group', 'image_group'],
            'reset': [t.JWP6 + 'file', t.JWP6 + 'playlistid']
        },
        'file': {
            'show' : ['file_group', 'image_yesno_group'],
            'hide' : ['mediaid_group', 'playlistid_group', 'imageid_group', 'image_group'],
            'reset': [t.JWP6 + 'mediaid', t.JWP6 + 'playlistid']
        },
        'playlistid': {
            'show' : ['playlistid_group'],
            'hide' : ['mediaid_group', 'file_group', 'image_yesno_group', 'imageid_group', 'image_group'],
            'reset': [t.JWP6 + 'mediaid', t.JWP6 + 'file']
        },
        'imageid': {
            'show' : ['imageid_group'],
            'hide' : ['image_group', 'image_yesno_group'],
            'reset': [t.JWP6 + 'image']
        },
        'image': {
            'show' : ['image_group'],
            'hide' : ['imageid_group', 'image_yesno_group'],
            'reset': [t.JWP6 + 'imageid']
        },
        'image_yesno': {
            'show' : ['image_yesno_group'],
            'hide' : ['imageid_group', 'image_group']
        },
        // Media manager
        'mm_thumb_url': {
            'show' : ['thumb_url_group'],
            'hide' : ['thumb_select_group'],
            'reset': [t.JWP6 + 'the_image_url'],
            'bind': 'setThumbnailValue'
        },
        'mm_thumb_select': {
            'show' : ['thumb_select_group'],
            'hide' : ['thumb_url_group'],
            'reset': [t.JWP6 + 'the_image_id'],
            'bind' : 'setThumbnailValue'
        }
    }

    this.toggle = function (elements, show_or_hide) {
        for (var i = 0; i < elements.length; i++) {
            if (show_or_hide && 'hide' == show_or_hide) {
                $('#' + elements[i]).addClass('hidden');
            } else {
                $('#' + elements[i]).removeClass('hidden');
            }
        };
        return false;
    }

    this.fieldset_toggle = function (e) {
        e.stopPropagation();
        var parts = e.target.href.split('#');
        if (parts.length <= 1) {
            return false;
        }
        var hash = parts[1];
        if (t.fieldset_toggles[hash]) {
            if (t.fieldset_toggles[hash]['show']) t.toggle(t.fieldset_toggles[hash]['show']);
            if (t.fieldset_toggles[hash]['hide']) t.toggle(t.fieldset_toggles[hash]['hide'], 'hide');
            if (t.fieldset_toggles[hash]['reset']) {
                for (var i = 0; i < t.fieldset_toggles[hash]['reset'].length; i++) {
                    $('#' + t.fieldset_toggles[hash]['reset'][i]).val('');
                    $('select#' + t.fieldset_toggles[hash]['reset'][i]).select2('val', '');
                }
            }
            if (t.fieldset_toggles[hash]['bind']) {
                t[t.fieldset_toggles[hash]['bind']]();
            }
        }
        //t.preview_player();
        return false;
    }

    this.init_fieldset_toggles = function () {
        $('a.fieldset_toggle').bind('click.fieldset_toggle', this.fieldset_toggle);
    }

    this.select2_change = function(e) {
    };

    this.preview_player = function () {
        var
            data          = {},
            player_name   = $('#player_name').select2("val"),
            mediaid       = $('#' + t.JWP6 + 'mediaid').select2("val"),
            file          = $('#' + t.JWP6 + 'file').val(),
            playlistid    = $('#' + t.JWP6 + 'playlistid').select2("val"),
            imageid       = $('#' + t.JWP6 + 'imageid').select2("val"),
            image         = $('#' + t.JWP6 + 'image').val()
        ;
        if ( mediaid || file || playlistid ) {
            data['player_name'] = player_name;
            if (playlistid) {
                data[t.JWP6 + 'playlistid'] = playlistid;
            }
            else if (file) {
                data[t.JWP6 + 'file'] = file;
            }
            else {
                data[t.JWP6 + 'mediaid'] = mediaid;
            }
            if (image) {
                data[t.JWP6 + 'image'] = image;
            }
            else if (imageid) {
                data[t.JWP6 + 'imageid'] = imageid;
            }
            $.post(
                JWP6_AJAX_URL + '?call=embedcode',
                data,
                function (data) {
                    $('#player-preview').html(data);
                }
            );
        } else {
            $('#player-preview').html('<p class="info">The preview of the player will show after you select a player and a video/video url/playlist.</p>');
        }
    };

    this.init_media_wizard = function () {
        $('#' + t.JWP6 + 'image, #' + t.JWP6 + 'file').bind('change', t.preview_player);
        t.init_fieldset_toggles();
    };

    this.setThumbnailValue = function () {
        var img_val = $('#' + t.JWP6 + 'the_image_value'),
            id_val = $('#' + t.JWP6 + 'the_image_id').val();
        if (id_val) {
            img_val.val(id_val);
        } else {
            img_val.val($('#' + t.JWP6 + 'the_image_url').val());
        }
    };


    this.insert_with_jwp6 = function (e) {
        alert('The URL will be: ' + $(e.target).data('url'));
        return false;
    }

}

var jwp6media = new JWP6Media();
jwp6media.init_media_wizard();
