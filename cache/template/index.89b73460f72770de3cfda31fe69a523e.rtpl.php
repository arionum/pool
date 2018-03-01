<?php if(!class_exists('Rain\Tpl')){exit;}?>                <!-- Quick Links -->
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <div class="panel panel-tile info-block info-block-bg-success">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-5 ph10 text-center ">
                                        <i class="fa fa-fire"></i>
                                    </div>
                                    <div class="col-xs-7 pl35 prn text-center">
                                        <h2><?php echo htmlspecialchars( $height, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                                        <h6>Current Block</h6>

                                    </div>
                                    <div class="col-sm-12">
                                        <div class="info-block-stat">
                                            <span>Last won block</span>
                                            <span><?php echo htmlspecialchars( $lastwon, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="panel panel-tile info-block info-block-bg-info">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-5 ph10 text-center">
                                        <i class="imoon imoon-users2"></i>
                                    </div>
                                    <div class="col-xs-7 pl35 text-center">
                                        <h2 class=""><?php echo htmlspecialchars( $miners, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                                        <h6 class="text-muted">Miners</h6>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="info-block-stat">
                                            <span>Difficulty</span>
                                            <span><?php echo htmlspecialchars( $difficulty, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="panel panel-tile info-block info-block-bg-warning">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-5 ph10 text-center">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="col-xs-7 pl35 text-center">
                                        <h2 class=""><?php echo htmlspecialchars( $total_paid, ENT_COMPAT, 'UTF-8', FALSE ); ?>M</h2>
                                        <h6 class="text-muted">ARO</h6>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="info-block-stat">
                                            <span>Total paid</span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="panel panel-tile info-block info-block-bg-system">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-5 ph10 text-center">
                                        <i class="fa fa-line-chart"></i>
                                    </div>
                                    <div class="col-xs-7 pl35 text-center">
                                        <h2 class=""><?php echo htmlspecialchars( $total_hr, ENT_COMPAT, 'UTF-8', FALSE ); ?></h2>
                                        <h6 class="text-muted">Hash Rate (<?php echo htmlspecialchars( $hr_ext, ENT_COMPAT, 'UTF-8', FALSE ); ?>)</h6>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="info-block-stat">
                                            <span>Average HR /s</span>
                                            <span><?php echo htmlspecialchars( $avg_hr, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AllCP Info -->
            

             
                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-lg-12 allcp-grid ">

                            <!-- Widget Calendar -->
          
                            <div class="panel of-h" id="spy1">
                                <div class="panel-heading br-b-o w-mt-15">
                                    <span class="panel-title">Current Shares</span>
                                    <span class="panel-title pull-right">Total Current Shares: <?php echo htmlspecialchars( $total_shares, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                </div>
                                <div class="panel-body pn mt25">
                                    <div class="table-responsive">
                                        <table class="table display datatable" id="datatable1">
                                            <thead>
                                            <tr>
                                                <th class="va-m">Shares</th>
                                                <th class="va-m">%</th>
                                                <th class="va-m">Best DL</th>
						<th class="va-m">Miner</th>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                            <tr>
                                                <th class="va-m">Miner</th>
                                                <th class="va-m">Shares</th>
                                                <th class="va-m">%</th>
                                                <th class="va-m">Best DL</th>
						<th class="va-m">Miner</th>
                                            </tr>
                                            </tr>
                                            </tfoot>

                                            <tbody>
<?php $counter1=-1;  if( isset($shares) && ( is_array($shares) || $shares instanceof Traversable ) && sizeof($shares) ) foreach( $shares as $key1 => $value1 ){ $counter1++; ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars( $value1["shares"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                                <td><?php echo htmlspecialchars( $value1["percent"], ENT_COMPAT, 'UTF-8', FALSE ); ?> %</td>
                                                <td><?php echo htmlspecialchars( $value1["bestdl"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
						<td><?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                            </tr>
<?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">

                            
                            <div class="panel of-h" id="spy1">
                                <div class="panel-heading br-b-o w-mt-15">
                                    <span class="panel-title">Historic Shares</span>
                                    <span class="panel-title pull-right">Total Historic Shares: <?php echo htmlspecialchars( $total_historic, ENT_COMPAT, 'UTF-8', FALSE ); ?></span>
                                </div>
                                <div class="panel-body pn mt25">
                                    <div class="table-responsive">
                                        <table class="table display datatable" id="datatable2">
                                            <thead>
                                            <tr>
                                            <tr>
                                            <th class="va-m">Shares</th>
                                            <th class="va-m">%</th>
 					    <th class="va-m">Hash Rate</th>
	<th class="va-m">Pending</th>
                                            <th class="va-m">Total paid</th>
						<th class="va-m">Miner</th>
                                        </tr>
                                            </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                            <tr>
                                                <th class="va-m">Shares</th>
 <th class="va-m">%</th>

                                                <th class="va-m">Hash Rate</th>
<th class="va-m">Pending</th>
                                                <th class="va-m">Total paid</th>
						<th class="va-m">Miner</th>
                                            </tr>
                                            </tr>
                                            </tfoot>
                                            <tbody>
<?php $counter1=-1;  if( isset($historic) && ( is_array($historic) || $historic instanceof Traversable ) && sizeof($historic) ) foreach( $historic as $key1 => $value1 ){ $counter1++; ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars( $value1["historic"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                                    <td><?php echo htmlspecialchars( $value1["percent"], ENT_COMPAT, 'UTF-8', FALSE ); ?> %</td>
						    <td><?php echo htmlspecialchars( $value1["hashrate"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
						    <td><?php echo htmlspecialchars( $value1["pending"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                                    <td><?php echo htmlspecialchars( $value1["total_paid"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                                    <td><?php echo htmlspecialchars( $value1["id"], ENT_COMPAT, 'UTF-8', FALSE ); ?></td>
                                                </tr>
<?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
    
                                </div>
                            </div>
                            
                           
                        </div>
                    </div>
