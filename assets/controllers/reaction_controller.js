import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['count', 'button', 'selector'];
    static values = {
        postId: Number,
        url: String
    }

    connect() {
        this.selectorTarget.style.display = 'none';
    }

    showSelector(event) {
        event.preventDefault();
        this.selectorTarget.style.display = 
            this.selectorTarget.style.display === 'none' ? 'flex' : 'none';
    }

    async react(event) {
        const type = event.currentTarget.dataset.type;
        const response = await fetch(this.urlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ type })
        });

        if (response.ok) {
            const data = await response.json();
            
            // Update counts
            for (const [type, count] of Object.entries(data)) {
                if (type !== 'userReaction') {
                    const countElement = this.element.querySelector(`.reaction-count-${type}`);
                    if (countElement) {
                        countElement.textContent = count;
                    }
                }
            }
            
            // Update active state on reaction buttons
            this.buttonTargets.forEach(button => {
                const buttonType = button.dataset.type;
                if (buttonType === data.userReaction) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
            
            // Update main button
            const mainButton = this.element.querySelector('.main-reaction-button');
            const typeDisplay = data.userReaction || 'Like';
            const emojiMap = {
                'like': '👍',
                'bravo': '👏',
                'soutien': '💪',
                'instructif': '💡',
                'drole': '😄'
            };
            
            mainButton.innerHTML = `${emojiMap[data.userReaction] || '👍'} ${typeDisplay}`;
            
            // Hide selector
            this.selectorTarget.style.display = 'none';
        }
    }
}