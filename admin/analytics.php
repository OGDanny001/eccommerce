<?php
$pageTitle = "Sales Analytics";
$activePage = "analytics";
require_once '../includes/admin-header.php';
?>

<div style="margin-bottom: 2rem;">
    <h2 style="margin-bottom: 0.5rem;">Sales Analytics & Insights</h2>
    <p style="color: #64748b; margin: 0;">Real-time overview of your store's performance</p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="admin-card" style="border-left: 4px solid #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #64748b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Revenue</p>
                <h3 id="stat-revenue" style="font-size: 2rem; margin: 0.5rem 0 0; color: #1e293b;">$0.00</h3>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 0.75rem; background: #dbeafe; display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.5rem;">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    
    <div class="admin-card" style="border-left: 4px solid #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #64748b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Orders</p>
                <h3 id="stat-orders" style="font-size: 2rem; margin: 0.5rem 0 0; color: #1e293b;">0</h3>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 0.75rem; background: #d1fae5; display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.5rem;">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>
    </div>
    
    <div class="admin-card" style="border-left: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #64748b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Customers</p>
                <h3 id="stat-customers" style="font-size: 2rem; margin: 0.5rem 0 0; color: #1e293b;">0</h3>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 0.75rem; background: #fef3c7; display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.5rem;">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="admin-card" style="border-left: 4px solid #8b5cf6;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p style="color: #64748b; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Avg Order Value</p>
                <h3 id="stat-aov" style="font-size: 2rem; margin: 0.5rem 0 0; color: #1e293b;">$0.00</h3>
            </div>
            <div style="width: 50px; height: 50px; border-radius: 0.75rem; background: #ede9fe; display: flex; align-items: center; justify-content: center; color: #8b5cf6; font-size: 1.5rem;">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Monthly Revenue Chart -->
    <div class="admin-card">
        <h3 style="margin-bottom: 1.5rem;">Monthly Revenue</h3>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
    
    <!-- Order Status Pie Chart -->
    <div class="admin-card">
        <h3 style="margin-bottom: 1.5rem;">Order Status</h3>
        <canvas id="statusChart" height="100"></canvas>
    </div>
</div>

<!-- Top Selling Products -->
<div class="admin-card">
    <h3 style="margin-bottom: 1.5rem;">Top Selling Products</h3>
    <canvas id="productsChart" height="80"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
// Load analytics data and render charts
let analyticsData;
let revenueChart, statusChart, productsChart;

async function loadAnalytics() {
    try {
        const response = await fetch('/eccommerce/api/analytics.php');
        analyticsData = await response.json();
        
        if (analyticsData.success) {
            // Update stat cards
            document.getElementById('stat-revenue').textContent = 
                '$' + analyticsData.overview.total_revenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('stat-orders').textContent = analyticsData.overview.total_orders.toLocaleString();
            document.getElementById('stat-customers').textContent = analyticsData.overview.total_customers.toLocaleString();
            document.getElementById('stat-aov').textContent = 
                '$' + analyticsData.overview.avg_order_value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Render charts
            renderRevenueChart();
            renderStatusChart();
            renderProductsChart();
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
    }
}

function renderRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const months = analyticsData.monthly_revenue.map(m => m.month);
    const revenues = analyticsData.monthly_revenue.map(m => m.revenue);
    
    if (revenueChart) revenueChart.destroy();
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: revenues,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { callback: (value) => '$' + value.toLocaleString() } } }
        }
    });
}

function renderStatusChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    const labels = [];
    const data = [];
    const colors = [];
    
    const statusColors = {
        'pending': '#f59e0b',
        'paid': '#3b82f6',
        'shipped': '#8b5cf6',
        'delivered': '#10b981'
    };
    
    for (let status in analyticsData.status_breakdown) {
        labels.push(status.charAt(0).toUpperCase() + status.slice(1));
        data.push(analyticsData.status_breakdown[status]);
        colors.push(statusColors[status] || '#64748b');
    }
    
    if (statusChart) statusChart.destroy();
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{ data: data, backgroundColor: colors, borderWidth: 0 }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}

function renderProductsChart() {
    const ctx = document.getElementById('productsChart').getContext('2d');
    const names = analyticsData.top_products.map(p => p.name);
    const revenues = analyticsData.top_products.map(p => p.total_revenue);
    
    if (productsChart) productsChart.destroy();
    
    productsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: names,
            datasets: [{
                label: 'Revenue',
                data: revenues,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { callback: (value) => '$' + value.toLocaleString() } } }
        }
    });
}

// Load data on page load
loadAnalytics();
</script>

<?php require_once '../includes/admin-footer.php'; ?>
