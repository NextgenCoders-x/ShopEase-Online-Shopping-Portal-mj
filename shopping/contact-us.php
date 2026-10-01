<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/header-new.php');
?>

<div class="se-container se-section">
    <div class="se-breadcrumb">
        <a href="index.php">Home</a> <span>/</span> <span class="current">Contact Us</span>
    </div>
    
    <div class="grid-2" style="gap: var(--space-3xl);">
        <div>
            <h2 class="se-section-title">Get In Touch</h2>
            <p style="color: var(--gray-600); margin-bottom: var(--space-xl); font-size: 15px; line-height: 1.7;">
                Have questions about our products, orders, or policies? Our customer support team is here to help you. Send us a message and we'll respond as soon as possible.
            </p>
            
            <div style="margin-bottom: var(--space-lg);">
                <div style="font-weight: 700; margin-bottom: 5px; font-size: 16px;"><i class="bi bi-geo-alt" style="color: var(--primary);"></i> Our Office</div>
                <div style="color: var(--gray-600);">123 Shopping Street, E-commerce City, 10001</div>
            </div>
            
            <div style="margin-bottom: var(--space-lg);">
                <div style="font-weight: 700; margin-bottom: 5px; font-size: 16px;"><i class="bi bi-telephone" style="color: var(--primary);"></i> Phone Support</div>
                <div style="color: var(--gray-600);">+1 234 567 8900 (Mon-Fri, 9am - 6pm)</div>
            </div>
            
            <div style="margin-bottom: var(--space-lg);">
                <div style="font-weight: 700; margin-bottom: 5px; font-size: 16px;"><i class="bi bi-envelope" style="color: var(--primary);"></i> Email Us</div>
                <div style="color: var(--gray-600);">support@shopease.com</div>
            </div>
        </div>
        
        <div class="se-card">
            <div class="se-card-header">Send a Message</div>
            <div class="se-card-body">
                <form action="#" method="post">
                    <div class="se-form-group">
                        <label>Your Name <span class="required">*</span></label>
                        <input type="text" class="se-input" required>
                    </div>
                    <div class="se-form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" class="se-input" required>
                    </div>
                    <div class="se-form-group">
                        <label>Subject</label>
                        <input type="text" class="se-input">
                    </div>
                    <div class="se-form-group">
                        <label>Message <span class="required">*</span></label>
                        <textarea class="se-textarea" rows="5" required></textarea>
                    </div>
                    <button type="button" class="se-btn se-btn-primary se-btn-lg se-btn-full mt-2" onclick="alert('Message sent successfully! We will get back to you soon.');">Send Message <i class="bi bi-send"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer-new.php'); ?>
