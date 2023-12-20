export class TaskAttachment {
    id: string;
    name: string;
    path: string;

    constructor(data: any) {
        this.id = data.id;
        this.name = data.name;
        this.path = data.path;
    }
}
