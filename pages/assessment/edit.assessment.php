<?php
session_start();
include '../../includes/head.php';
include 'accountConn/conn.php';
include '../../includes/session.php';

$stud_no = $_GET['stud_no'];

?>
<title>
    Edit New Assessment | SFAC - Bacoor
</title>
</head>

<body class="g-sidenav-show  bg-gray-100">
    <?php include '../../includes/sidebar.php'; ?>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
        <!-- Navbar -->
        <?php include '../../includes/navbar-title.php'; ?>
        <h6 class="font-weight-bolder mb-0">Edit Assessment</h6>
        <?php include '../../includes/navbar.php'; ?>
        <!-- End Navbar -->

        <div class="container-fluid py-4">
            <div class="row mb-10">
                <div class="col-lg-9 col-12 mx-auto">
                    <div class="card card-body mt-4 shadow-sm">
                        <h5 class="font-weight-bolder mb-0">Edit Assessment</h5>
                        <p class="text-sm mb-0">Assessment Details</p>
                        <hr class="horizontal dark my-3">
                        <form method="POST" enctype="multipart/form-data" action="userData/ctrl.edit.assessment.php?stud_no=<?php echo $stud_no?>">
                            <?php
                                $studInfo = mysqli_query($db, "SELECT *,CONCAT(tbl_students.lastname, ', ', tbl_students.firstname, ' ', tbl_students.middlename)  as fullname FROM tbl_schoolyears 
                                LEFT JOIN tbl_students ON tbl_students.stud_id = tbl_schoolyears.stud_id
                                LEFT JOIN tbl_courses ON tbl_courses.course_id = tbl_schoolyears.course_id 
                                WHERE tbl_students.stud_no = '$stud_no' AND ay_id = '$_SESSION[AC]' AND sem_id = '$_SESSION[S]' AND remark = 'Approved'") or die (mysqli_error($db));
                                while ($row1 = mysqli_fetch_array($studInfo)) {

                                    $unittotal = mysqli_query($db, "SELECT SUM(unit_total) AS total_unit FROM tbl_enrolled_subjects
                                    LEFT JOIN tbl_subjects_new ON tbl_subjects_new.subj_id = tbl_enrolled_subjects.subj_id
                                    WHERE tbl_enrolled_subjects.stud_id = '$row1[stud_id]' AND tbl_enrolled_subjects.acad_year = '$_SESSION[AC]' AND tbl_enrolled_subjects.semester = '$_SESSION[S]'") or die (mysqli_error($db));

                                    while ($row2 = mysqli_fetch_array($unittotal)) {
                                        $total_unit = $row2['total_unit'];
                                    }

                                    $tuitionInfo = mysqli_query($acc, "SELECT tuition_fee, tbl_tuition_fees.tf_id, payment, tbl_assessed_tf.created_at, disc_id, lab_units, lab_id FROM tbl_assessed_tf
                                    LEFT JOIN tbl_tuition_fees ON tbl_tuition_fees.tf_id = tbl_assessed_tf.tf_id
                                    WHERE tbl_assessed_tf.course_id = '$row1[course_id]' AND tbl_assessed_tf.ay_id = '$_SESSION[AYear]' AND year_id = '$row1[year_id]'") or die (mysqli_error($acc));

                                    while ($row3 = mysqli_fetch_array($tuitionInfo)) {
                                        $tuition_fee = $row3['tuition_fee'];
                                        $tf_id = $row3['tf_id'];
                                        $payment = $row3['payment'];
                                        $discount_array = explode(",",$row3['disc_id']);
                                        $lab_array = explode(",",$row3['lab_id']);
                                        $units = explode(",",$row3['lab_units']);
                                    }
                                    $total_tuition =($tuition_fee * $total_unit);
                    
                            ?>
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Student Name</label>
                                        <input class="form-control" type="text" value="<?php echo $row1['fullname'];?>"
                                            name="discount_desc" disabled />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="mt-3">Academic Year</label>
                                        <select class="form-control" id="academic_year" disabled>
                                            <?php $getEAY = mysqli_query($db, "SELECT * FROM tbl_acadyears WHERE ay_id = '$_SESSION[AYear]'");
                                            while ($row = mysqli_fetch_array($getEAY)) {
                                                $ay_id = $row['ay_id'];
                                            ?>
                                            <option selected value="<?php echo $row['ay_id']; ?>">
                                                <?php echo $row['academic_year'];
                                            } ?></option>
                                        </select>
                                        <input type="text" name="ay_id" value="<?php echo $ay_id; ?>" hidden>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="mt-3">Course</label>
                                        <input class="form-control" type="text" value="<?php echo $row1['course_abv'];?>"
                                            name="discount" disabled/>
                                        <input type="text" name="course_id" value="<?php echo $row1['course_id']?>" hidden>
                                        
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mt-3">Total Unit</label>
                                        <input class="form-control" type="text" value="<?php echo $total_unit;?>"
                                            name="discount" disabled/>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mt-3">Tuition Fee per Unit</label>
                                        <input class="form-control" type="text" value="Php <?php echo number_format($tuition_fee, 2);?>"
                                            name="discount" disabled/>
                                            <input type="text" name="tf_id" value="<?php echo $tf_id; ?>" hidden>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="mt-3">Total Tuition Fee</label>
                                        <input class="form-control" type="text" value="Php <?php echo number_format($total_tuition, 2);?>"
                                            name="discount" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="mt-3">Payment</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="cash" name="payments[]" <?php echo ($payment == 'cash' ? 'checked' : '');?>>
                                            <label>Cash</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="trimestral" name="payments[]" <?php echo ($payment == 'trimestral' ? 'checked' : '');?>>
                                            <label>Trimestral</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="quarterly" name="payments[]" <?php echo ($payment == 'quarterly' ? 'checked' : '');?>>
                                            <label>Quarterly</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="mt-3">Laboratories</label>
                                        <?php
                                            $i = 0;
                                            $selectLab = mysqli_query($acc,"SELECT lab_id, lab, lab_desc FROM tbl_lab_fees WHERE year_id = '$row1[year_id]' AND ay_id = '$_SESSION[AYear]'");
                                            while ($row5 = mysqli_fetch_array($selectLab)) {

                                        ?>
                                        <div class="form-check">
                                            <div class="row">
                                            <div class="col-sm-4">
                                            <input class="form-check-input" type="checkbox" value="<?php echo $row5['lab_id']?>" name="lab[]" <?php echo (in_array($row5['lab_id'], $lab_array) ? 'checked ': '');?>>
                                            <label><?php echo $row5['lab_desc']?></label>
                                            </div>
                                            <div class="col-sm-3">
                                            <input class="form-control form-control-sm" name="index[]" type="number" placeholder="no. of units" <?php echo (in_array($row5['lab_id'], $lab_array) ? 'value="'.$units[$i].'"' .$i++: '');?>> 
                                            </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        ?>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="mt-3">Discounts</label>
                                        
                                            <?php
                                                $selectDiscount = mysqli_query($acc, "SELECT disc_id ,discount, discount_desc, percent FROM tbl_discounts WHERE ay_id = '$_SESSION[AYear]'");
                                                while ($row4 = mysqli_fetch_array($selectDiscount)) {

                                            ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="<?php echo $row4['disc_id'];?>" name="discounts[]" <?php echo (in_array($row4['disc_id'], $discount_array) ? 'checked' : '');?>>
                                            <label><?php echo $row4['discount_desc'];?> - <?php echo ($row4['percent']== 1 ? $row4['discount'].'%' : 'Php '.number_format($row4['discount'], 2));?></label>
                                        </div>
                                            <?php
                                                }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn bg-gradient-dark text-white m-0 ms-2" type="submit" title="Send"
                                    name="submit">Edit
                                    Assessment</button>
                            </div>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php include '../../includes/footer.php'; ?>
            <!-- End footer -->
        </div>
    </main>
    <!--   Core JS Files   -->
    <?php include '../../includes/scripts.php'; ?>
</body>

</html>