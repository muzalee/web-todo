export class Task {
    id: string;
    title: string;
    description: string;
    dueDate: string | null;
    priorityId: number | null;
    priorityName: string | null;
    completedAt: string | null;
    archivedAt: string | null;

    constructor(data: any) {
      this.id = data.id;
      this.title = data.title;
      this.description = data.description;
      this.dueDate = data.due_date;
      this.priorityId = data.priorityId ?? data.priority?.id;
      this.priorityName = data.priorityName ?? data.priority?.name;
      this.completedAt = data.completed_at;
      this.archivedAt = data.archived_at;
    }
}
