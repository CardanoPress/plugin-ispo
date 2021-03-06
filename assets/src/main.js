import { handleDelegation, handleTracking } from './actions'

window.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine || {}
    const cardanoPress = window.cardanoPress || {}

    Alpine.data('cardanoPressISPO', () => ({
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

        async init() {
            this.ration = parseInt(this.$root.dataset.ration)
            this.minimum = parseInt(this.$root.dataset.minimum)
            this.maximum = parseInt(this.$root.dataset.maximum)
            this.commence = parseInt(this.$root.dataset.commence)
            this.conclude = parseInt(this.$root.dataset.conclude)
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

            if (this.epochs > (this.limit)) {
                this.epochs = this.limit
            }

            return ((this.ration / 100) * this.delegate * this.epochs).toFixed(6)
        },

        async handleDelegation() {
            this.transactionHash = ''

            cardanoPress.api.addNotice({
                id: 'ispo-delegation',
                type: 'info',
                text: 'Processing...',
            })

            this.isProcessing = true
            const response = await handleDelegation()

            cardanoPress.api.removeNotice('ispo-delegation')

            if (response.success) {
                this.transactionHash = response.data.hash

                cardanoPress.api.addNotice({ type: 'info', text: response.data.message })
            } else {
                cardanoPress.api.addNotice({ type: 'warning', text: response.data })
            }

            this.isProcessing = false
        },

        async handleTracking() {
            this.trackedReward = ''

            cardanoPress.api.addNotice({
                id: 'ispo-tracking',
                type: 'info',
                text: 'Tracking...',
            })

            this.isProcessing = true
            const response = await handleTracking(this.address)

            cardanoPress.api.removeNotice('ispo-tracking')

            if (response.success) {
                this.trackedReward = response.data.toFixed(6)
            } else {
                cardanoPress.api.addNotice({ type: 'warning', text: response.data })
            }

            this.isProcessing = false
        },
    }))
})
