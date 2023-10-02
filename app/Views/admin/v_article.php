                    <?php
                    $halaman = 'article';
                    ?>
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        <?= $templateJudul ?>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 pt-1 pb-1">
                                <form action="" method="GET">
                                    <input type="text" placeholder="Katakunci..." name="katakunci" class="form-control" value="<?= $katakunci ?>">
                                </form>
                            </div>
                            <div class="col-lg-9 pt-1 pb-1 text-end">
                                <a class="btn btn-xl btn-primary" href="<?= site_url('admin/'.$halaman.'/tambah') ?>">+ Tambah</a>
                            </div>
                        </div>
                        <?php
                        $session = \Config\Services::session();
                        if($session->getFlashdata('warning')){
                        ?>
                            <div class="alert alert-warning">
                                <ul>
                                    <?php 
                                    foreach($session->getFlashdata('warning') as $val){
                                    ?>
                                        <li><?= $val ?></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php 
                        }
                        if($session->getFlashdata('success')){
                        ?>
                            <div class="alert alert-success"><?= $session->getFlashdata('success') ?></div>
                        <?php 
                        }
                        ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="col-1">No.</th>
                                    <th class="col-6">Judul</th>
                                    <th class="col-3">Tanggal</th>
                                    <th class="col-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($record as $value){
                                    $post_id = $value['post_id'];
                                    $link_delete = site_url("admin/$halaman/?aksi=hapus&post_id=$post_id");
                                    $link_edit = site_url("admin/$halaman/edit/$post_id");
                                ?>
                                    <tr>
                                        <td><?= $nomor ?></td>
                                        <td><?= $value['post_title'] ?></td>
                                        <td><?= tanggal_indonesia($value['post_time']); ?></td>
                                        <td>
                                            <a href="<?= $link_edit ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="<?= $link_delete ?>" onclick="return confirm('Yakin akan menghapus data ini?')" class="btn btn-sm btn-danger">Del</a>
                                        </td>
                                    </tr>
                                <?php
                                $nomor++;
                                }
                                ?>
                            </tbody>
                        </table>
                        <?= $pager->links('dt', 'datatable'); ?>
                    </div>