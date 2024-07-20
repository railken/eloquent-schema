
        /**
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new class ($class, "temp") extends NodeVisitorAbstract {
            protected string $class;
            protected string $as;

            public function __construct($class, $as = null) {
                $this->class = $class;
                $this->as = $as;
            }
            public function leaveNode(Node $node): void {

                if ($node instanceof Node\Stmt\Namespace_) {
                    $factory = new BuilderFactory;
                    $node->stmts = array_merge([$factory->use($this->class)], $node->stmts);
                }
            }
        });
         * */

        // https://github.com/ajthinking/archetype/blob/master/src/Endpoints/PHP/Use_.php
        $stmts = $this->file->ast();
        $stmts = $traverser->traverse($stmts);

        $this->updateFile($stmts);

        $this->save();

        return $this;

    /**
     * Interact with the user's first name.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => $value,
        );
    }