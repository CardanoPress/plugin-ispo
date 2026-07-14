import type { Alpine as AlpineType } from 'alpinejs'

declare global {
    interface Window {
        Alpine: AlpineType
        cardanoPress: Record<string, string | any>
        cardanoPressISPOMessages: Record<string, string>
    }
}

export { }
