<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">About Us</span>
    </div>
    
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
        <h2 class="se-section-title" style="justify-content: center;">About ShopEase</h2>
        <p style="color: var(--gray-600); font-size: 16px; line-height: 1.8; margin-bottom: var(--space-xl);">
            Welcome to ShopEase, your number one source for all things modern shopping. We're dedicated to giving you the very best of products, with a focus on dependability, customer service and uniqueness.
        </p>
    </div>
    
    <div class="grid-2" style="gap: var(--space-3xl); margin-top: var(--space-xl);">
        <div>
            <h3 style="margin-bottom: 15px; font-size: 22px;">Our Story</h3>
            <p style="color: var(--gray-700); font-size: 15px; line-height: 1.7; margin-bottom: 15px;">
                Founded in 2023, ShopEase has come a long way from its beginnings. When we first started out, our passion for helping customers find the best quality products at affordable prices drove us to do intense research and gave us the impetus to turn hard work and inspiration into a booming online store.
            </p>
            <p style="color: var(--gray-700); font-size: 15px; line-height: 1.7;">
                We now serve customers all over the country and are thrilled to be a part of the fair trade wing of the e-commerce industry. We hope you enjoy our products as much as we enjoy offering them to you.
            </p>
        </div>
        
        <div class="se-card" style="background: var(--primary-50); border: none; padding: var(--space-xl); display: flex; flex-direction: column; justify-content: center;">
            <h3 style="margin-bottom: 15px; font-size: 20px; color: var(--primary-dark);">Our Core Values</h3>
            <ul style="list-style: none; padding: 0; color: var(--gray-800); font-size: 15px; line-height: 2;">
                <li><i class="bi bi-check2-circle" style="color: var(--primary); margin-right: 10px;"></i> <b>Quality First</b>: We never compromise on product quality.</li>
                <li><i class="bi bi-check2-circle" style="color: var(--primary); margin-right: 10px;"></i> <b>Customer Focus</b>: Your satisfaction is our priority.</li>
                <li><i class="bi bi-check2-circle" style="color: var(--primary); margin-right: 10px;"></i> <b>Integrity</b>: We believe in honest pricing and policies.</li>
                <li><i class="bi bi-check2-circle" style="color: var(--primary); margin-right: 10px;"></i> <b>Innovation</b>: Constantly improving our platform.</li>
            </ul>
        </div>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
