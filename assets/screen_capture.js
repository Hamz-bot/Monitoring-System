class ScreenCapture {
    constructor(userId) {
        this.userId = userId;
        this.captureInterval = null;
        this.minInterval = 20 * 60 * 1000; // 20 minutes in milliseconds
        this.maxInterval = 30 * 60 * 1000; // 30 minutes in milliseconds
    }
    
    start() {
        this.scheduleNextCapture();
    }
    
    stop() {
        if (this.captureInterval) {
            clearTimeout(this.captureInterval);
            this.captureInterval = null;
        }
    }
    
    scheduleNextCapture() {
        // Generate random interval between 20-30 minutes
        const interval = Math.floor(Math.random() * (this.maxInterval - this.minInterval + 1)) + this.minInterval;
        
        this.captureInterval = setTimeout(() => {
            this.captureScreen();
        }, interval);
    }
    
    async captureScreen() {
        try {
            // Request screen capture permission
            const stream = await navigator.mediaDevices.getDisplayMedia({
                video: { mediaSource: 'screen' }
            });
            
            // Create video element to capture the stream
            const video = document.createElement('video');
            video.srcObject = stream;
            video.play();
            
            // Wait for video to be ready
            video.onloadedmetadata = () => {
                // Create canvas to draw the video frame
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Stop the stream
                stream.getTracks().forEach(track => track.stop());
                
                // Convert canvas to data URL
                const imageData = canvas.toDataURL('image/jpeg');
                
                // Send to server
                this.sendScreenshotToServer(imageData);
                
                // Schedule next capture
                this.scheduleNextCapture();
            };
        } catch (err) {
            console.error("Error capturing screen:", err);
            // Schedule next capture even if this one failed
            this.scheduleNextCapture();
        }
    }
    
    sendScreenshotToServer(imageData) {
        const formData = new FormData();
        formData.append('user_id', this.userId);
        formData.append('image_data', imageData);
        
        fetch('save_auto_screenshot.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Automatic screenshot captured successfully');
            } else {
                console.error('Failed to capture screenshot:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Get user ID from meta tag
    const userIdElement = document.querySelector('meta[name="user-id"]');
    if (userIdElement) {
        const userId = userIdElement.getAttribute('content');
        if (userId) {
            const screenCapture = new ScreenCapture(userId);
            screenCapture.start();
            
            // Clean up when page unloads
            window.addEventListener('beforeunload', () => {
                screenCapture.stop();
            });
        }
    }
});