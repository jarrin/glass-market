// Test if tabs are working
console.log('=== TAB DEBUG INFO ===');
console.log('Profile tabs found:', document.querySelectorAll('.profile-tab').length);
console.log('Tab panels found:', document.querySelectorAll('.tab-panel').length);

document.querySelectorAll('.profile-tab').forEach((tab, index) => {
    console.log(`Tab ${index}:`, tab.getAttribute('data-tab'), 'Active:', tab.classList.contains('active'));
});

document.querySelectorAll('.tab-panel').forEach((panel, index) => {
    console.log(`Panel ${index}:`, panel.id, 'Active:', panel.classList.contains('active'));
});

// Test clicking the subscription tab
console.log('\nTrying to activate subscription tab...');
const subTab = document.querySelector('[data-tab="subscription"]');
if (subTab) {
    console.log('Subscription tab found, clicking it...');
    subTab.click();
    setTimeout(() => {
        const subPanel = document.getElementById('tab-subscription');
        console.log('Subscription panel active?', subPanel ? subPanel.classList.contains('active') : 'Panel not found');
    }, 100);
} else {
    console.error('Subscription tab button not found!');
}
