@extends('layouts.app')

@section('title', 'Dashboard')

@push('plugin-js')
<script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/vendors/jquery-circle-progress/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.cookie.js') }}" type="text/javascript"></script>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endpush

@section('content')
<div class="d-xl-flex justify-content-between align-items-start">
  <h2 class="text-dark font-weight-bold mb-2"> Overview dashboard </h2>
  <div class="d-sm-flex justify-content-xl-between align-items-center mb-2">
    <div class="btn-group bg-white p-3" role="group" aria-label="Basic example">
      <button type="button" class="btn btn-link text-gray py-0 border-right">7 Days</button>
      <button type="button" class="btn btn-link text-dark py-0 border-right">1 Month</button>
      <button type="button" class="btn btn-link text-gray py-0">3 Month</button>
    </div>
    <div class="dropdown ms-0 ml-md-4 mt-2 mt-lg-0">
      <button class="btn bg-white dropdown-toggle p-3 d-flex align-items-center" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-calendar me-1"></i>24 Mar 2019 - 24 Mar 2019 </button>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton1">
        <h6 class="dropdown-header">Settings</h6>
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <a class="dropdown-item" href="#">Something else here</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Separated link</a>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <i class="mdi mdi-cube text-danger icon-lg"></i>
          </div>
          <div class="float-end">
            <p class="mb-0 text-right text-dark">Total Revenue</p>
            <div class="fluid-container">
              <h3 class="font-weight-medium text-right mb-0 text-dark">$6,560</h3>
            </div>
          </div>
        </div>
        <p class="text-muted mt-3 mb-0">
          <i class="mdi mdi-alert-octagon me-1" aria-hidden="true"></i> 65% lower growth </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <i class="mdi mdi-receipt text-warning icon-lg"></i>
          </div>
          <div class="float-end">
            <p class="mb-0 text-right text-dark">Orders</p>
            <div class="fluid-container">
              <h3 class="font-weight-medium text-right mb-0 text-dark">3455</h3>
            </div>
          </div>
        </div>
        <p class="text-muted mt-3 mb-0">
          <i class="mdi mdi-bookmark-outline me-1" aria-hidden="true"></i> Product-wise sales </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <i class="mdi mdi-poll-box text-success icon-lg"></i>
          </div>
          <div class="float-end">
            <p class="mb-0 text-right text-dark">Sales</p>
            <div class="fluid-container">
              <h3 class="font-weight-medium text-right mb-0 text-dark">5693</h3>
            </div>
          </div>
        </div>
        <p class="text-muted mt-3 mb-0">
          <i class="mdi mdi-calendar me-1" aria-hidden="true"></i> Weekly Sales </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics">
      <div class="card-body">
        <div class="clearfix">
          <div class="float-start">
            <i class="mdi mdi-account-box-multiple text-info icon-lg"></i>
          </div>
          <div class="float-end">
            <p class="mb-0 text-right text-dark">Employees</p>
            <div class="fluid-container">
              <h3 class="font-weight-medium text-right mb-0 text-dark">246</h3>
            </div>
          </div>
        </div>
        <p class="text-muted mt-3 mb-0">
          <i class="mdi mdi-reload me-1" aria-hidden="true"></i> Product-wise sales </p>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
      <div class="card-body pb-0">
        <p class="text-dark">Total Invoice</p>
        <div class="d-flex align-items-center">
          <h4 class="font-weight-semibold text-dark">$65,650</h4>
          <h6 class="text-success font-weight-semibold ms-2">+876</h6>
        </div>
        <small class="text-muted">This has been a great update.</small>
      </div>
      <canvas class="mt-2 chartjs-render-monitor" height="40" id="statistics-graph-1" width="375" style="display: block;"></canvas>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
      <div class="card-body pb-0">
        <p class="text-dark">Total expenses</p>
        <div class="d-flex align-items-center">
          <h4 class="font-weight-semibold text-dark">$65,650</h4>
          <h6 class="text-danger font-weight-semibold ms-2">-43</h6>
        </div>
        <small class="text-muted">view statement</small>
      </div>
      <canvas class="mt-2 chartjs-render-monitor" height="40" id="statistics-graph-3" width="375" style="display: block;"></canvas>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
      <div class="card-body pb-0">
        <p class="text-dark">Unpaid Invoices</p>
        <div class="d-flex align-items-center">
          <h4 class="font-weight-semibold text-dark">$2,542</h4>
          <h6 class="text-success font-weight-semibold ms-2">+876</h6>
        </div>
        <small class="text-muted">view history</small>
      </div>
      <canvas class="mt-2 chartjs-render-monitor" height="40" id="statistics-graph-2" width="375" style="display: block;"></canvas>
    </div>
  </div>
  <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
    <div class="card card-statistics"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
      <div class="card-body pb-0">
        <p class="text-dark">Amount Due</p>
        <div class="d-flex align-items-center">
          <h4 class="font-weight-semibold text-dark">$3450</h4>
          <h6 class="text-success font-weight-semibold ms-2">+23</h6>
        </div>
        <small class="text-muted">65% lower growth</small>
      </div>
      <canvas class="mt-2 chartjs-render-monitor" height="40" id="statistics-graph-4" width="375" style="display: block;"></canvas>
    </div>
  </div>
  <div class="col-12 grid-margin">
    <div class="card card-statistics">
      <div class="row">
        <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
              <i class="mdi mdi-account-multiple-outline text-primary me-0 me-sm-4 icon-lg"></i>
              <div class="wrapper text-center text-sm-left">
                <p class="card-text mb-0 text-dark">New Users</p>
                <div class="fluid-container">
                  <h3 class="mb-0 font-weight-medium text-dark">65,650</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
              <i class="mdi mdi-checkbox-marked-circle-outline text-primary me-0 me-sm-4 icon-lg"></i>
              <div class="wrapper text-center text-sm-left">
                <p class="card-text mb-0 text-dark">New Feedbacks</p>
                <div class="fluid-container">
                  <h3 class="mb-0 font-weight-medium text-dark">32,604</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
              <i class="mdi mdi-trophy-outline text-primary me-0 me-sm-4 icon-lg"></i>
              <div class="wrapper text-center text-sm-left">
                <p class="card-text mb-0 text-dark">Employees</p>
                <div class="fluid-container">
                  <h3 class="mb-0 font-weight-medium text-dark">17,583</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-col col-xl-3 col-lg-3 col-md-3 col-6">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-center flex-column flex-sm-row">
              <i class="mdi mdi-target text-primary me-0 me-sm-4 icon-lg"></i>
              <div class="wrapper text-center text-sm-left">
                <p class="card-text mb-0 text-dark">Total Sales</p>
                <div class="fluid-container">
                  <h3 class="mb-0 font-weight-medium text-dark">61,119</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="d-sm-flex justify-content-between align-items-center transaparent-tab-border {">
      <ul class="nav nav-tabs tab-transparent" role="tablist">
        <li class="nav-item">
          <a class="nav-link" id="home-tab" data-bs-toggle="tab" href="#" role="tab" aria-selected="true">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" id="business-tab" data-bs-toggle="tab" href="#business-1" role="tab" aria-selected="false">Business</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="performance-tab" data-bs-toggle="tab" href="#" role="tab" aria-selected="false">Performance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="conversion-tab" data-bs-toggle="tab" href="#" role="tab" aria-selected="false">Conversion</a>
        </li>
      </ul>
      <div class="d-md-block d-none">
        <a href="#" class="text-light p-1"><i class="mdi mdi-view-dashboard"></i></a>
        <a href="#" class="text-light p-1"><i class="mdi mdi-dots-vertical"></i></a>
      </div>
    </div>
    <div class="tab-content tab-transparent-content">
      <div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
        <div class="row">
          <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body text-center">
                <h5 class="mb-2 text-dark font-weight-normal">Orders</h5>
                <h2 class="mb-4 text-dark font-weight-bold">932.00</h2>
                <div class="dashboard-progress dashboard-progress-1 d-flex align-items-center justify-content-center item-parent"><i class="mdi mdi-lightbulb icon-md absolute-center text-dark"></i></div>
                <p class="mt-4 mb-0">Completed</p>
                <h3 class="mb-0 font-weight-bold mt-2 text-dark">5443</h3>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body text-center">
                <h5 class="mb-2 text-dark font-weight-normal">Unique Visitors</h5>
                <h2 class="mb-4 text-dark font-weight-bold">756,00</h2>
                <div class="dashboard-progress dashboard-progress-2 d-flex align-items-center justify-content-center item-parent"><i class="mdi mdi-account-circle icon-md absolute-center text-dark"></i></div>
                <p class="mt-4 mb-0">Increased since yesterday</p>
                <h3 class="mb-0 font-weight-bold mt-2 text-dark">50%</h3>
              </div>
            </div>
          </div>
          <div class="col-xl-3  col-lg-6 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body text-center">
                <h5 class="mb-2 text-dark font-weight-normal">Impressions</h5>
                <h2 class="mb-4 text-dark font-weight-bold">100,38</h2>
                <div class="dashboard-progress dashboard-progress-3 d-flex align-items-center justify-content-center item-parent"><i class="mdi mdi-eye icon-md absolute-center text-dark"></i></div>
                <p class="mt-4 mb-0">Increased since yesterday</p>
                <h3 class="mb-0 font-weight-bold mt-2 text-dark">35%</h3>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-sm-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body text-center">
                <h5 class="mb-2 text-dark font-weight-normal">Followers</h5>
                <h2 class="mb-4 text-dark font-weight-bold">4250k</h2>
                <div class="dashboard-progress dashboard-progress-4 d-flex align-items-center justify-content-center item-parent"><i class="mdi mdi-cube icon-md absolute-center text-dark"></i></div>
                <p class="mt-4 mb-0">Decreased since yesterday</p>
                <h3 class="mb-0 font-weight-bold mt-2 text-dark">25%</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <h4 class="card-title mb-0">Recent Activity</h4>
                      <div class="dropdown dropdown-arrow-none">
                        <button class="btn p-0 text-dark dropdown-toggle" type="button" id="dropdownMenuIconButton1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="mdi mdi-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuIconButton1">
                          <h6 class="dropdown-header">Settings</h6>
                          <a class="dropdown-item" href="#">Action</a>
                          <a class="dropdown-item" href="#">Another action</a>
                          <a class="dropdown-item" href="#">Something else here</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="#">Separated link</a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-sm-4 grid-margin  grid-margin-lg-0">
                    <div class="wrapper pb-5 border-bottom">
                      <div class="text-wrapper d-flex align-items-center justify-content-between mb-2">
                        <p class="mb-0 text-dark">Total Profit</p>
                        <span class="text-success"><i class="mdi mdi-arrow-up"></i>2.95%</span>
                      </div>
                      <h3 class="mb-0 text-dark font-weight-bold">$ 92556</h3>
                      <canvas id="total-profit"></canvas>
                    </div>
                    <div class="wrapper pt-5">
                      <div class="text-wrapper d-flex align-items-center justify-content-between mb-2">
                        <p class="mb-0 text-dark">Expenses</p>
                        <span class="text-success"><i class="mdi mdi-arrow-up"></i>52.95%</span>
                      </div>
                      <h3 class="mb-4 text-dark font-weight-bold">$ 59565</h3>
                      <canvas id="total-expences"></canvas>
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-8 grid-margin  grid-margin-lg-0">
                    <div class="ps-0 ps-lg-4 ">
                      <div class="d-xl-flex justify-content-between align-items-center mb-2">
                        <div class="d-lg-flex align-items-center mb-lg-2 mb-xl-0">
                          <h3 class="text-dark font-weight-bold me-2 mb-0">Devices sales</h3>
                          <h5 class="mb-0">( growth 62% )</h5>
                        </div>
                        <div class="d-lg-flex">
                          <p class="me-2 mb-0">Timezone:</p>
                          <p class="text-dark font-weight-bold mb-0">GMT-0400 Eastern Delight Time</p>
                        </div>
                      </div>
                      <div class="graph-custom-legend clearfix" id="device-sales-legend"></div>
                      <canvas id="device-sales"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-4 grid-margin stretch-card">
            <div class="card card-danger-gradient">
              <div class="card-body mb-4">
                <h4 class="card-title text-white">Account Retention</h4>
                <canvas id="account-retension"></canvas>
              </div>
              <div class="card-body bg-white pt-4">
                <div class="row pt-4">
                  <div class="col-sm-6">
                    <div class="text-center border-right border-md-0">
                      <h4>Conversion</h4>
                      <h1 class="text-dark font-weight-bold mb-md-3">$306</h1>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-center">
                      <h4>Cancellation</h4>
                      <h1 class="text-dark font-weight-bold">$1,520</h1>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-8  grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <div class="d-xl-flex justify-content-between mb-2">
                  <h4 class="card-title">Page views analytics</h4>
                  <div class="graph-custom-legend primary-dot" id="pageViewAnalyticLengend"></div>
                </div>
                <canvas id="page-view-analytic"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection