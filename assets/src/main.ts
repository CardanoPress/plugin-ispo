import { handleDelegation, handleTracking } from './actions'

const cardanoPressISPOMessages = window.cardanoPressISPOMessages || {
    delegating: '',
    tracking: '',
}

window.addEventListener('alpine:init', () => {
    window.Alpine.data('cardanoPressISPO', () => ({
        isProcessing: false,
        ration: 1,
        minimum: 1,
        maximum: 2,
        commence: 1,
        conclude: 2,
        control: 1,
        limit: 2,
        delegate: 1,
        epochs: 1,
        address: '',
        trackedReward: '',
        transactionHash: '',
        extraRewards: null as any,

        async init() {
            this.ration = parseFloat(this.$root.dataset.ration || '0')
            this.minimum = parseInt(this.$root.dataset.minimum || '0')
            this.maximum = parseInt(this.$root.dataset.maximum || '0')
            this.commence = parseInt(this.$root.dataset.commence || '0')
            this.conclude = parseInt(this.$root.dataset.conclude || '0')
            this.delegate = this.minimum
            this.limit = this.control + this.conclude - this.commence

            console.log('CardanoPress ISPO ready!')
        },

        getRewards() {
            if (this.delegate < this.minimum) {
                this.delegate = this.minimum
            }

            if (this.delegate > this.maximum) {
                this.delegate = this.maximum
            }

            if (this.epochs < this.control) {
                this.epochs = this.control
            }

            if (this.epochs > this.limit) {
                this.epochs = this.limit
            }

            return ((this.ration / 100) * this.delegate * this.epochs).toFixed(6)
        },

        async handleDelegation() {
            this.transactionHash = ''

            window.cardanoPress.api.addNotice({
                id: 'ispo-delegation',
                type: 'info',
                text: cardanoPressISPOMessages.delegating,
            })

            this.isProcessing = true

            try {
                const response = await handleDelegation()

                window.cardanoPress.api.removeNotice('ispo-delegation')

                if (response.success) {
                    this.transactionHash = response.data.hash

                    window.cardanoPress.api.addNotice({ type: 'info', text: response.data.message })
                } else {
                    window.cardanoPress.api.addNotice({ type: 'warning', text: response.data })
                }
            } catch (error) {
                window.cardanoPress.api.removeNotice('ispo-delegation')
                window.cardanoPress.api.addNotice({
                    type: 'error',
                    text: error instanceof Error ? error.message : 'Request failed',
                })
            } finally {
                this.isProcessing = false
            }
        },

        async handleTracking() {
            this.trackedReward = ''
            this.extraRewards = null

            window.cardanoPress.api.addNotice({
                id: 'ispo-tracking',
                type: 'info',
                text: cardanoPressISPOMessages.tracking,
            })

            this.isProcessing = true

            try {
                const response = await handleTracking(this.address)

                window.cardanoPress.api.removeNotice('ispo-tracking')

                if (response.success) {
                    this.trackedReward = response.data.amount.toFixed(6)
                    this.extraRewards = response.data.extra

                    window.cardanoPress.api.addNotice({ type: 'info', text: response.data.message })
                } else {
                    window.cardanoPress.api.addNotice({ type: 'warning', text: response.data })
                }
            } catch (error) {
                window.cardanoPress.api.removeNotice('ispo-tracking')
                window.cardanoPress.api.addNotice({
                    type: 'error',
                    text: error instanceof Error ? error.message : 'Request failed',
                })
            } finally {
                this.isProcessing = false
            }
        },
    }))
})
