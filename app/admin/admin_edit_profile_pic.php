<?php
require '../_base.php';

// Get member ID from URL parameter
$id = req('id');

if (is_get()) {
    $member_id = req('member_id');

    $stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
    $stm->execute([$id]);
    $member = $stm->fetch();

    if (!$member) {
        temp('info', 'Member not found.');
        redirect('member_list.php');
    }

    extract((array)$member);
    $_SESSION['member_profile_pic'] = $member->member_profile_pic;
}

if (is_post()) {
    $photo = get_file('member_profile_pic');
    $member_profile_pic = $_SESSION['member_profile_pic'];

    if (count($_err) == 0) {
        if ($photo) {
            unlink("../photos/$member_profile_pic");
            $member_profile_pic = save_photo($photo, '../photos');
        }

        $stm = $_db->prepare('UPDATE member SET member_profile_pic = ? WHERE member_id = ?');
        $stm->execute([$member_profile_pic, $id]);

        temp('info', 'Member profile picture updated.');
        redirect('member_list.php');
    }
    else {
        temp('info', 'Please check the error(s).');
    }

}

// ----------------------------------------------------------------------------
$_title = 'Admin | Edit Member Profile Picture';
include '../_head.php';
?>

<form method="post" class="form" enctype="multipart/form-data">
    <label for="member_id">Member ID</label>
    <b><?= $id ?></b>
    <?= err('member_id') ?>

    <label for="member_name">Name</label>
    <?= html_text('member_name', 'maxlength="100" disabled') ?>
    <?= err('member_name') ?>

    <label for="member_phone_no">Phone No</label>
    <?= html_text('member_phone_no', 'maxlength="11" disabled') ?>
    <?= err('member_phone_no') ?>

    <label for="member_gender">Gender</label>
    <?= html_radios('member_gender', $_genders, 'disabled') ?>
    <?= err('member_gender') ?>

    <label for="member_email">Email</label>
    <?= html_text('member_email', 'maxlength="100" disabled') ?>
    <?= err('member_email') ?>

    <label for="shipping_address">Shipping Address</label>
    <?= html_text('shipping_address', 'width=500px disabled') ?>
    <?= err('shipping_address') ?>

    <label for="member_profile_pic">Profile Picture</label>
    <label class="upload" tabindex="0">
        <?= html_file('member_profile_pic', 'image/*', 'hidden') ?>
        <img src="../photos/<?= $member_profile_pic ?>">
    </label>
    <?= err('member_profile_pic') ?>

    <section>
        <button type="submit">Save</button>
        <a href="member_list.php">Cancel</a>
    </section>
</form>


<pre>
    <?php
    // print_r($_SESSION);
    // print_r($_FILES);
    ?>