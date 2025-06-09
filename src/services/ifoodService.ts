export class IfoodService {
    private apiUrl: string;
    private apiKey: string;

    constructor() {
        this.apiUrl = process.env.IFOOD_API_URL || '';
        this.apiKey = process.env.IFOOD_API_KEY || '';
    }

    async fetchOrders(): Promise<any> {
        const response = await fetch(`${this.apiUrl}/orders`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${this.apiKey}`,
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch orders from iFood API');
        }

        return response.json();
    }

    async updateMenu(menuData: any): Promise<any> {
        const response = await fetch(`${this.apiUrl}/menu`, {
            method: 'PUT',
            headers: {
                'Authorization': `Bearer ${this.apiKey}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(menuData)
        });

        if (!response.ok) {
            throw new Error('Failed to update menu in iFood API');
        }

        return response.json();
    }
}